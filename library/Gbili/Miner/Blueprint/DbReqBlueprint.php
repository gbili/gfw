<?php
namespace Gbili\Miner\Blueprint;

/**
 * The Blueprint takes a host as constructor parameter and with that it will
 * try to reconstruct a previously saved (with \\Gbili\\Miner\\Blueprint_Savable)
 * action tree. For that it queries the storage (for example the database) using the
 * DbRegistry which must return an instanceof \\Gbili\\Miner\\Blueprint_Db_Interface.
 * @see DbRegistry
 * The returned instance contains all the information the blueprint needs to create
 * an action tree. The action tree may contain two types of actions :
 * 1) Extract
 * 		Extracts bits (parts) of data from the plain text other actions pass to it.
 * 		It takes input either from Extract or GetContents actions.
 * 2) GetContents
 * 		Gets the text from the web, given a string url.
 * 		It takes its input from root data or Extract actions.
 * The blueprint constructs this tree
 * 
 * 
 * 
 * @author gui
 *
 */
class DbReqBlueprint
extends AbstractBlueprint
implements BlueprintInterface
{
	/**
	 * Miner_Persistance::run(), will generate instances
	 * where the actions results will be inserted.
	 * This tells at which action (from id) \\Gbili\\Miner\\Blueprint
	 * has to call setAsNewInstanceGeneratingPoint()
	 * so Miner_Persistance knows that when it reaches that
	 * action it has to generate a new instance.
	 * 
	 * @var unknown_type
	 */
	private $newInstanceGeneratingPointActionId;
	
	/**
	 * @var Host
	 */
	private $host;

    /**
     * @var Db\DbInterface
     */
    protected $dbReq;
	
	/**
	 * Will generate the actions chain from the action set
	 * from Db passed as argument
	 * This is called from \\Gbili\\Miner\\Blueprint::factory()
	 * 
	 * @param \Gbili\Url\Authority\Host $host
	 * @return unknown_type
	 */
	public function __construct(\Zend\ServiceManager\ServiceManager $sm)
	{
        $config = $sm->get('ApplicationConfig');
        if (!isset($config['host'])) {
            throw new Exception('Config must have a \'host\' key set with the hostname to be dumped');
        }
        $host = $config['host'];
        
        if (is_string($host)) {
            $host = new \Gbili\Url\Authority\Host($host);
        }

        if (!$host instanceof \Gbili\Url\Authority\Host) {
            throw new Exception('First param must be a valid url string or a Host instance.');
        }
		$this->host = $host;

	    $this->setServiceManager($sm);
	}

    /**
     * Create the action stack
     *
     * @return self
     */
    public function init()
    {
		$this->setNewInstanceGeneratingPoint();
		$this->addActionSet();
		$this->manageInjections();
    }

    public function setDbReq(DbReqBlueprint\Db\DbInterface $dbReq)
    {
        $this->dbReq = $dbReq;
        return $this;
    }

    public function getDbReq()
    {
        if (null === $this->dbReq) {
            throw new Exception('Requestor not set');
        }
        return $this->dbReq;
    }

    protected function addActionSet()
    {
        $recordset = $this->getDbReq()->getActionSet($this->host);
		if (!$recordset) {
			throw new Exception('There is no action set for this url : '. $this->host->toString());
		}
		foreach ($recordset as $row) {
            $row['isNewInstanceGeneratingPoint'] = ((integer)$row['actionId'] === (integer)$this->newInstanceGeneratingPointActionId);
            $action = $this->createActionFromRow($row);
            $this->addAction($action);
		}
    }
	
	/**
     * Actions can take input from other than parent
     * Here is where those injections are setup
	 */
	protected function manageInjections()
	{
	    foreach ($this->getActions() as $action) {
    		if (!$injectData = $this->getDbReq()->getInjectionData($action->getId())) continue;
            $injectData               = current($injectData);
            $injectingAction          = $this->getAction($injectData['injectingActionId']);
            $getInputFromactionGroup  = $injectData['inputGroup'];
            $action->setInputActionInfo($injectingAction, $getInputFromactionGroup);
	    }
	}
	
	/**
	 * 
	 */
    protected function getNewAction(array $row)
    {
        $type = (integer) $row['type'];
        if ($type === self::ACTION_TYPE_EXTRACT) {
            $handle = 'Extract';
        }
        if ($type === self::ACTION_TYPE_GETCONTENTS) {
            $handle = (($this->isActionRoot($row))? 'Root': '') . 'GetContents';
        }
        if (!isset($handle)) {
            throw new Exception('Unsupported action type given : ' . print_r($row, true));
        }
        return $this->getServiceManager()->get('Action' . $handle);
    }

	/**
	 * 
	 * @param array $row
	 * @throws Exception
	 */
	protected function createActionFromRow(array $row)
	{
        $action = $this->getNewAction($row);
	    $action->setBlueprint($this);
        $row['actionId'] = $row['title'];
        $action->hydrate($row);
        return $action;
	}

	/**
	 * Set the method and callback instances
	 * and the new instance generating point action id
	 * 
	 * @return unknown_type
	 */
	private function setNewInstanceGeneratingPoint()
	{
		$recordset = $this->getDbReq()->getBlueprintInfo($this->host);
		if (false === $recordset || empty($recordset)) {
			throw new Exception('The blueprint does not exist');
		}
        $record = current($recordset);
        $id = (integer) $record['newInstanceGeneratingPointActionId'];
		if (0 === $id) {
            throw new Exception('New instance generating point should not be 0');
		}
        $this->newInstanceGeneratingPointActionId = $id;
	}

    protected function isActionRoot(array $info)
    {
        return $info['actionId'] === $info['parentId'];
    }

    /**
     * Init callback wrapper if the action provides info
     * for it
     * @param array $info blueprint info for action
     * @return self
     */
    public function initCallbackWrapperForAction(Blueprint\Action\GetContents $action)
    {
		if ($resultset = $this->getDbReq()->getActionCallable($action->getId())) {
            $callbackInfo = current($resultset);
            $this->setActionCallbackWrapper($action, $callbackInfo);
		}
        return $this;
    } 

    /**
     *
     */
    protected function setActionCallbackWrapper(Blueprint\Action\GetContents $action, $callbackInfo)
    {
        $service = $this->getServiceManager()->get($callbackInfo['serviceIdentifier']);

        $callbackWrapper = new Blueprint\Action\GetContents\Callback\Wrapper($service, $callbackInfo['methodName']);
 
        //not all callbacks have a mapping (ex: a root get contents that uses itself as input)
        $callbackParamGroupMapping = array();
        if ($callbackMapping = $this->getDbReq()->getActionCallableParamsToGroupMapping($action->getId())) {
            foreach ($callbackMapping as $callbackInfo) {
                $callbackParamGroupMapping[$callbackInfo['paramNum']] = $callbackInfo['regexGroup'];
            }
        }
        if (empty($callbackParamGroupMapping)) {
            $callbackParamGroupMapping = array(1);
        }
        $callbackWrapper->setParamToGroupMapping($callbackParamGroupMapping);
        $action->setCallbackWrapper($callbackWrapper);
    }
	
	/**
	 * 
	 * @param array $info
	 * @return void
	 */
	public function initActionExtract($action)
	{
		if ($groupMapping = $this->getDbReq()->getActionGroupToEntityMapping($action->getId())) {
			$action->setGroupMapping($groupMapping);
		}
        $this->initExtractMethodWrapper($action);
        return $action;
	}

    /**
     * Init extract method  wrapper if the action provides info
     * for it
     * @param array $info blueprint info for action
     * @return self
     */
    protected function initExtractMethodWrapper(Blueprint\Action\Extract $action)
    {
		if ($interceptMap = $this->getDbReq()->getActionGroupToCallableAndInterceptType($action->getId())) {
            $methodWrapper = new Blueprint\Action\Extract\Method\Wrapper($this->getServiceManager(), $interceptMap);
            $action->setMethodWrapper($methodWrapper);
		}
    }
	
	/**
	 * 
	 * @param numeric $actionId
	 * @param string $input
	 * @return void 
	 */
	public static function updateActionInputData($actionId, $input)
	{
		\Gbili\Db\Registry::getInstance('\Gbili\Miner\Blueprint\Action\Savable')->updateActionInputData($actionId, $input);
	}
}
