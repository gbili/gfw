<?php
namespace Gbili\Miner;

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
class Blueprint
{
	/**
	 * The type of action
	 * Extract
	 * 
	 * @var integer
	 */
	const ACTION_TYPE_EXTRACT = 12;
	
	/**
	 * The type of action
	 * GetContents
	 * 
	 * @var integer
	 */
	const ACTION_TYPE_GETCONTENTS = 13;
	
	/**
	 * 
	 * @var \Zend\ServiceManager\ServiceManager
	 */
	protected $serviceManager;
	
	/**
	 * this is a flat representation of the
	 * actions tree
	 * So it eases access to actions
	 * 
	 * @var unknown_type
	 */
	private $actionStack = array();
	
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
	public function __construct(\Gbili\Url\Authority\Host $host, \Zend\ServiceManager\ServiceManager $sm, Blueprint\Db\DbInterface $dbReq)
	{
		$this->host = $host;
	    $this->setServiceManager($sm);
        $this->setDbReq($dbReq);
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

    public function setDbReq(Blueprint\Db\DbInterface $dbReq)
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
	public function getServiceManager()
	{
	    return $this->serviceManager;
	}

	/**
	 * 
	 */
	public function setServiceManager(\Zend\ServiceManager\ServiceManager $sm)
    {
        $this->serviceManager = $sm;
	    return $this;
	}
	
    protected function getActionClass(array $row)
    {
        $baseClassName = '\\Gbili\\Miner\\Blueprint\\Action\\';
        $type = (integer) $row['type'];
        if ($type === self::ACTION_TYPE_EXTRACT) {
            $class = 'Extract';
        }
        if ($type === self::ACTION_TYPE_GETCONTENTS) {
            $class = 'GetContents' . (($this->isActionRoot($row))? '\\RootGetContents': '');
        }
        if (!isset($class)) {
            throw new Exception('Unsupported action type given : ' . print_r($row, true));
        }
        return $baseClassName . $class;
    }

	/**
	 * 
	 * @param array $row
	 * @throws Exception
	 */
	protected function createActionFromRow(array $row)
	{
        $actionClass = $this->getActionClass($row);
        $action = new $actionClass();
	    $action->setBlueprint($this);
        $action->hydrate($row);
        return $action;
	}

    /**
     * Add action to action stack indexed by their ids
     * and point the current action to the latest added action
     */
    protected function addAction(Blueprint\Action\AbstractAction $action)
    {
	    $this->actionStack[$action->getId()] = $action;
    }

    public function hasAction($id)
    {
        return isset($this->actionStack[(integer)$id]);
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
	 * Proxy
	 * 
	 * @return Blueprint\Action\RootAction 
	 */
	public function getRoot()
	{
        if (empty($this->actionStack)) {
		  	throw new Exception('There are no actions in blueprint, call init()');
		}
        reset($this->actionStack);
		return current($this->actionStack)->getRoot();
	}
	
	/**
	 * Returns the action with the id
	 * 
	 * @return Blueprint\Action\AbstractAction 
	 */
	public function getAction($id)
	{
		if (!$this->hasAction($id)) {
			throw new Exception("There is no action with id : $id in blueprint");
		}
		return $this->actionStack[(integer) $id];
	}

    /**
     *
     */
    public function getActions()
    {
        return $this->actionStack;
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
