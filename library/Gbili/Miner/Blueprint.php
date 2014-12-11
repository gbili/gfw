<?php
namespace Gbili\Miner;

use Gbili\Miner\Blueprint\Action\RootAction;
use Gbili\Miner\Blueprint\Action\GetContents\RootGetContents;


use Gbili\Out\Out,
    Gbili\Url\Authority\Host,
    Gbili\Db\Registry                                                as DbRegistry,
    Gbili\Miner\Blueprint\Db\DbInterface,
    Gbili\Miner\Blueprint\Action\AbstractAction,
    Gbili\Miner\Blueprint\Action\GetContents,
    Gbili\Miner\Blueprint\Action\Extract,
    Gbili\Miner\Blueprint\Action\Extract\Method\Wrapper       as ExtractMethodWrapper,
    Gbili\Miner\Blueprint\Action\GetContents\Callback\Wrapper as GetContentsCallbackWrapper;

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
	protected $serviceManager = null;
	
	/**
	 * Contains one action \\Gbili\\Miner\\Blueprint_Action_Abstractfrom
	 * which all other actions are accessible
	 * 
	 * @var \Gbili\Miner\Blueprint\Action\AbstractAction
	 */
	private $lastAction;
	
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
	private $newInstanceGeneratingPointActionId = null;
	
	/**
	 * Every time an action has been successfully added
	 * to the action chain, then this variable takes
	 * the value of its parent it
	 * So it can be remebered for next action, and help
	 * determining, if the next action is brother (has same parent)
	 * or is child
	 * 
	 * @var unknown_type
	 */
	private $lastParentId = null;
	
	/**
	 * 
	 * @var \Gbili\Miner\Blueprint\Action\AbstractAction
	 */
	private $currentAction = null;
	
	/**
	 * 
	 * @var Host
	 */
	private $host;
	
	/**
	 * Will generate the actions chain from the action set
	 * from Db passed as argument
	 * This is called from \\Gbili\\Miner\\Blueprint::factory()
	 * 
	 * @param Host $host
	 * @return unknown_type
	 */
	public function __construct(Host $host, \Zend\ServiceManager\ServiceManager $sm)
	{
	    $this->setServiceManager($sm);
		$this->host = $host;
		//get the blueprint info from db and set it in instance
		$this->setInfo();
		/*
		 * after calling setInfo()
		 * and there is new instance generating point action id
		 * fetch the Db to get the action set
		 */
		$recordset = DbRegistry::getInstance('\Gbili\Miner\Blueprint')->getActionSet($this->host);
		//ensure there are rows
		if (false === $recordset) {
			throw new Exception('There is no action set for this url : '. $this->host->toString());
		}
		
		//create an action per recordset row
		foreach ($recordset as $row) {
            $this->createActionFromRow($row);
		}
		
		//ensure there is a new instance generating point action is present
		if (null === $this->newInstanceGeneratingPointActionId) {
			throw new Exception( 'There is no new instance generating point');
		}
		
		//now pass the root pointer to the blueprint
		$this->lastAction = $this->currentAction->getRoot();
		
		$this->manageInjections();
	}
	
	/**
	 * 
	 */
	protected function manageInjections()
	{
	    foreach ($this->actionStack as $id => $action) {
    		$injectData = DbRegistry::getInstance('\Gbili\Miner\Blueprint')->getInjectionData($id);

            // @todo Possible bug: || is_array() should probably be || !is_array()
            // since $injectData is used as an array below this check
            // @important changed || is_array() to || !is_array()
    		if (empty($injectData) || (!is_array($injectData))) continue; 

            $injectData               = current($injectData);
            $injectingAction          = $this->actionStack[$injectData['injectingActionId']];
            $getInputFromactionGroup  = $injectData['inputGroup'];
            $action->setOtherInputActionInfo($injectingAction, $getInputFromactionGroup);
            $injectingAction->setInjectsParent();
	    }
	}
	
	/**
	 * @return \Zend\ServiceManager\ServiceManager
	 */
	public function getServiceManager()
	{
	    return $this->serviceManager;
	}

	/**
	 * @return 
	 */
	public function setServiceManager(\Zend\ServiceManager\ServiceManager $sm)
	{
	    $this->serviceManager = $sm;
        return $this;
	}
	
	/**
	 * 
	 * @param array $row
	 * @throws Exception
	 */
	protected function createActionFromRow(array $row)
	{	    
	    switch ((integer) $row['type']) {
	        case self::ACTION_TYPE_EXTRACT:
	            $this->initActionExtract($row);
	            break;
	        case self::ACTION_TYPE_GETCONTENTS:
	            $this->initActionGetContents($row);
	            break;
	        default:
	            throw new Exception('Unsupported action type given : ' . print_r($row, true));
	            break;
	    }
	    
	    $this->currentAction->setTitle($row['title']);
	    $this->currentAction->setId($row['actionId']);

	    if ($this->currentAction->getId() === $this->newInstanceGeneratingPointActionId) {
	        $this->currentAction->setAsNewInstanceGeneratingPoint();
	    }
   
	    $this->actionStack[(integer) $row['actionId']] = $this->currentAction;
   
	    $this->chainToParentAndSetInputGroupIfExtract((integer) $row['parentId'], $row['inputGroup']);

	    $this->currentAction->setBlueprint($this);
	    return $this->currentAction;
	}
	
	/**
	 * and the new instance generating point action id
	 * 
	 * @return unknown_type
	 */
	private function setInfo()
	{
		$dbReqObj = DbRegistry::getInstance('\\Gbili\\Miner\\Blueprint');
		if (!($dbReqObj instanceof DbInterface)) {
			throw new Exception('The DbRegistry must return an instanceof \\Gbili\\Miner\\Blueprint\\Db\\DbInterface');
		}
		$recordset = $dbReqObj->getBlueprintInfo($this->host);
		if (false === $recordset) {
			throw new Exception('The blueprint does not exist');
		}
		
        if (empty($recordset)) {
			throw new Exception('There are no records in db');
        }

        $record = current($recordset);

		//set the new instance generating point action id
		if (0 !== (integer) $record['newInstanceGeneratingPointActionId']) {
		    $this->newInstanceGeneratingPointActionId = (integer) $record['newInstanceGeneratingPointActionId'];
		}
	}
	
	/**
	 * This function creates an action instance that it makes
	 * available to the constructor by setting $this->currentAction
	 * 
	 * @param array $info
	 * @return unknown_type
	 */
	private function initActionGetContents(array $info)
	{
		//Out::l2("initializing action getContents\n");
		//if it is root action
		if ($info['actionId'] === $info['parentId']) {
		    $action = new RootGetContents();
		    $action->setBootstrapData($info['data']);
		} else {
		    $action = new GetContents();
		}
		
		$this->currentAction = $action;
		$this->currentAction->setAsOptional($info['isOpt']);

        $this->initCallbackWrapperForActionInInfo($info);
	}

    /**
     * Init callback wrapper if the action provides info
     * for it
     * @param array $info blueprint info for action
     * @return self
     */
    protected function initCallbackWrapperForActionInInfo(array $info)
    {
		$callbackInfo = DbRegistry::getInstance($this)->getActionCallbackMethodName($info['actionId']);
		if (empty($callbackInfo) || false === $callbackInfo) {
            return $this;
		}

		$row = current($callbackInfo);
		//Out::l1($row['methodName']);
		//if the callbacInstance is null it will throw an exception because of param type hint
		$callbackWrapper = new GetContentsCallbackWrapper($this->getServiceManager(), $row['methodName']);
 
		//not all callbacks have a mapping (ex: a root get contents that uses itself as input)
		$callbackMapping = DbRegistry::getInstance($this)->getActionCallbackParamsToGroupMapping($info['actionId']);
		//Out::l2("retrieving action callback params to group mapping : \n" . print_r($callbackMapping, true));
		$callbackParamGroupMapping = array();//set blank mapping
		if (false !== $callbackMapping) {
		    //reshape array
		    foreach ($callbackMapping as $row) {
		        $callbackParamGroupMapping[$row['paramNum']] = $row['regexGroup'];
		    }
		} else {
		    //default group mapping
		    $callbackParamGroupMapping = array(1);
		}
		$callbackWrapper->setParamToGroupMapping($callbackParamGroupMapping);
		$this->currentAction->setCallbackWrapper($callbackWrapper);

        return $this;
    } 
	
	/**
	 * 
	 * @param array $info
	 * @return void
	 */
	private function initActionExtract(array $info)
	{
		//Out::l2("initializing action extract\n");
		$this->currentAction = new Extract($info['data'], (1 === (integer) $info['useMatchAll']));
		//Out::l2("initActionExtract Id : {$info['actionId']}, regexData : " . print_r($info['data'], true));
		//query Db to get the group mapping (final results mapping)
		$groupMapping = DbRegistry::getInstance($this)->getActionGroupToEntityMapping($info['actionId']);
		//ensure there are rows
		if (false !== $groupMapping) {
			//set the group mapping even if empty
			$this->currentAction->setGroupMapping($groupMapping);
		}
		$this->currentAction->setAsOptional($info['isOpt']);

        $this->initExtractMethodForActionInInfo($info);
	}

    /**
     * Init extract method  wrapper if the action provides info
     * for it
     * @param array $info blueprint info for action
     * @return self
     */
    protected function initExtractMethodForActionInInfo(array $info)
    {
		$interceptMap = DbRegistry::getInstance($this)->getActionGroupToMethodNameAndInterceptType($info['actionId']);
		
		if (empty($interceptMap) || false === $interceptMap) {
		    return $this;
		}
		$mW = new ExtractMethodWrapper($this->getServiceManager(), $interceptMap);
		$this->currentAction->setMethodWrapper($mW);
        return $this;
    }
	
	/**
	 * Chains the action to the right parent
	 * and sets from which parent group the 
	 * action must take its input data (if it
	 * is an instance of extract)
	 * 
	 * @param $action
	 * @param $currentParentId
	 * @param $inputDataGroup
	 * @return unknown_type
	 */
	private function chainToParentAndSetInputGroupIfExtract($currentParentId, $inputDataGroup)
	{
		//find the right parent and add child
		if (!$this->currentAction instanceof RootAction) {
		    $this->findParent($currentParentId)->addChild($this->currentAction);
		}

		//Point the chain to the last action
		$this->lastAction = $this->currentAction;
		//also set the group for input
		if ($this->lastAction->getParent() instanceof Extract) {
			$this->lastAction->setInputDataGroup($inputDataGroup);
		}
	}
	
	/**
	 * 
	 * @param unknown_type $parentId
	 * @return \Gbili\Miner\Blueprint\Action\AbstractAction
	 */
	private function findParent($parentId)
	{
		//get the youngest action
		$action = $this->lastAction;
		while ($action->getId() !== $parentId) {
			if ($action instanceof RootAction) {
				throw new Exception("Did not find parent in stack while rolling back");
			}
			$action = $action->getParent();
		}
		return $action;
	}
	
	/**
	 * Proxy
	 * 
	 * @return unknown_type
	 */
	public function getRoot()
	{
		if ($this->lastAction === null || 
		  !($this->lastAction instanceof AbstractAction)) {
		  	throw new Exception('There are no actions in blueprint $this->lastAction : ' . print_r($this->lastAction, true));
		}
		return $this->lastAction->getRoot();
	}
	
	/**
	 * Returns the action with the id
	 * 
	 * @return unknown_type
	 */
	public function getAction($id)
	{
		if (!is_numeric($id)) {
		    throw new Exception('Id must be numeric, given: ' . gettype($id));
		}
		$id = (integer) $id;
		if (!isset($this->actionStack[$id])) {
			throw new Exception("There is no action with id : $id in blueprint");
		}
		return $this->actionStack[$id];
	}
	
	/**
	 * 
	 * @param unknown_type $actionId
	 * @param unknown_type $input
	 * @return unknown_type
	 */
	public static function updateActionInputData($actionId, $input)
	{
		DbRegistry::getInstance('\Gbili\Miner\Blueprint\Action\Savable')->updateActionInputData($actionId, $input);
	}
}
