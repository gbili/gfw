<?php
namespace Gbili\Miner\Blueprint\Action\Savable\Db;

use Gbili\Miner\Blueprint\Action\RootAction;

use Gbili\Miner\Blueprint\Action\GetContents\RootGetContents;

use Gbili\Db\Req\AbstractReq,
    Gbili\Db\Req\Exception,
    Gbili\Db\Registry,
    Gbili\Miner\Blueprint,
    Gbili\Miner\Blueprint\Action\Savable\AbstractSavable,
    Gbili\Miner\Blueprint\Action\Extract\Savable     as ExtractSavable,
    Gbili\Miner\Blueprint\Action\GetContents\Savable as GetContentsSavable;

/**
 * 
 * @author gui
 *
 */
class Req
extends AbstractReq
{
	
	const DEFAULT_NO_INPUT_PARENT_REGEX_GROUP_NUMBER = 0;
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * For the actions of type extract, the result is returned
	 * as an indexed array with the group number as key and the
	 * result as value.
	 * For that type of array results, this function will return
	 * an array mapping the group number in result to the name
	 * of the entity.
	 * Ex : extract result : array(0=>'whole group', 1=>'Big lebowsky', 2=>'johny depp')
	 * 		mapping : array(1=>'Title', 2=>'Actor')
	 * then the two arrays should be combined to get an array like
	 * 		final : array('Title'=>'Big Lebowsky', 'Actor'=>'Johnny Depp')
	 * but this is done from blueprint, not from here.
	 * This returns the mapping array.
	 * 
	 * @param integer $actionId the id of the action in Db
	 * @return array
	 */
	private function setActionGroupToEntityMapping(array $mapping, $actionId)
	{
		$sql = "INSERT 
					INTO BAction_RegexGroup_r_Const
						(bActionId, regexGroup, const, isOpt)
					VALUES ";
		$varToValues = array();
		foreach ($mapping as $k => $arr) {
			$sql .= '(?, ?, ?, ?),';
			$varToValues[] = $actionId;
			$varToValues[] = $arr['regexGroup'];//group integer
			$varToValues[] = $arr['entity'];//const
			$varToValues[] = (integer) $arr['isOpt'];//optional is bool change it to 0 or 1
		}
		$sql = mb_substr($sql, 0, -1); //remove the trailing ","
		$this->insertUpdateData($sql, $varToValues);
	}
	
	/**
	 * 
	 * @param array $mapping
	 * @param unknown_type $actionId
	 * @param unknown_type $bId
	 * @return unknown_type
	 */
	private function setActionGroupToCallableMapping(array $mapping, $actionId, $bId)
	{
		$sql = "INSERT
				INTO BAction_RegexGroup_r_Callable
					(bActionId, regexGroup, methodId, interceptType) VALUES ";
		$varToValues = array();
		foreach ($mapping as $k => $arr) {
			$sql .= '(?, ?, ?, ?),';
			$varToValues[] = $actionId;
			$varToValues[] = (integer) $arr['regexGroup'];//group integer
			$varToValues[] = $this->saveCallableAndGetId($arr['callable'], $bId);
			$varToValues[] = (integer) $arr['interceptType'];//optional is bool change it to 0 or 1
		}
		$sql = mb_substr($sql, 0, -1); //remove the trailing ","
		$this->insertUpdateData($sql, $varToValues);
	}
	
	/**
	 * 
	 * @param unknown_type $callable
	 * @param unknown_type $bId
	 * @return unknown_type
	 */
	private function saveCallableAndGetId($callable)
	{
		if (false === $id = $this->existsCallable($callable, true)) {
			$sql = "INSERT INTO Blueprint_Callable (bId, name) VALUES (:bId, :callable)";
			$this->insertUpdateData($sql, array('bId' => $bId,':callable' => $callable));
			$id = $this->getAdapter()->lastInsertId();
		}
		return $id;
	}
	
	/**
	 * 
	 * @param unknown_type $methodName
	 * @param unknown_type $bId
	 * @param unknown_type $returnIdOrFalse
	 * @return unknown_type
	 *
	private function existsCallable($callable, $returnIdOrFalse)
	{
		$sql = "SELECT m.methodId AS methodId
					FROM Callable AS m
					WHERE m.bId = :bId AND m.name = :methodName";
		return $this->existsElement($sql,
									array(':bId' => (integer) $bId,
										  ':methodName' => $methodName),
                                          (($returnIdOrFalse)? 'methodId' : null));
	}*/
	
	/**
	 * 
	 * @param array $mapping
	 * @param unknown_type $actionId
	 * @return unknown_type
	 */
	private function saveCallbackMapping(array $mapping, $actionId)
	{
		$sql = "INSERT INTO BAction_RegexGroup_r_Callable_ParamNum (bActionId, paramNum, regexGroup) VALUES ";
		$varToValues = array();
		foreach ($mapping as $paramNum => $group) {
			$sql .= '(?, ?, ?),';
			$varToValues[] = $actionId;
			$varToValues[] = $paramNum;
			$varToValues[] = $group;
		}
		$sql = mb_substr($sql, 0, -1);//remove trailing ','
		$this->insertUpdateData($sql, $varToValues);
	}
	
	/**
	 * 
	 * @param unknown_type $methodName
	 * @param unknown_type $actionId
	 * @return unknown_type
	 */
	private function saveCallable($callable)
	{
		if ($id = $this->existsCallable($callable)) {
            return $id;
		}

        $params = array();

        if (is_string($callable)) {
            $callable = array($callable);
        }
        if (!is_array($callable)) {
            throw new \Exception('Callable must be string or array of strings');
        }
        switch (count($callable)) {
            case 2;
                list($serviceIdentifier, $methodName) = $callable;
                $sql = "INSERT INTO Callable (serviceIdentifier, methodName) VALUES (:serviceIdentifier, :methodName)";
                $params[':methodName'] = $methodName;
                break;
            case 1;
                $sql = "INSERT INTO Callable (serviceIdentifier) VALUES (:serviceIdentifier)";
                $serviceIdentifier = current($callable);
                break;
            default;                 
                throw new \Exception('Callable array must contain serviceIdentifier and methodName if not directly invokable');
                break;
        }
        $params[':serviceIdentifier'] = $serviceIdentifier;
        $this->insertUpdateData($sql, $params);
	}
	
	/**
	 * 
	 * @param unknown_type $methodName
	 * @param unknown_type $actionId
	 * @return unknown_type
	 */
	private function existsCallable($callable)
	{
		$sql = "SELECT c.methodName AS methodName
					FROM Callable AS c
					WHERE c.serviceIdentifier = :serviceIdentifier";
        $params = array();

        if (is_string($callable)) {
            $callable = array($callable);
        }
        if (!is_array($callable)) {
            throw new \Exception('Callable must be string or array of strings');
        }
        switch (count($callable)) {
            case 2;
                list($serviceIdentifier, $methodName) = $callable;
                $sql .= ' AND c.methodName = :methodName';
                $params[':methodName'] = $methodName;
                break;
            case 1;
                $serviceIdentifier = current($callable);
                break;
            default;                 
                throw new \Exception('Callable array must contain serviceIdentifier and methodName if not directly invokable');
                break;
        }
        $params[':serviceIdentifier'] = $serviceIdentifier;
		return $this->existsElement($sql, $params, 'callableId');
	}
	
	/**
	 * 
	 * @param unknown_type $actionId
	 * @return unknown_type
	 */
	private function existsAction($actionId, $returnIdOrFalse = false)
	{
		if (!is_numeric($actionId)) {
			throw new Exception("the action id must be numeric, given : " . print_r($actionId, true));
		}
		$sql = "SELECT a.bActionId AS actionId
					FROM BAction AS a
					WHERE a.bActionId = :actionId";
		return $this->existsElement($sql,
									array(':actionId' => (integer) $actionId),
									(($returnIdOrFalse)? 'actionId' : null));
	}
	
	/**
	 * 
	 * @param AbstractSavable $action
	 * @return unknown_type
	 */
	private function insertActionAndSetId(AbstractSavable $action)
	{
		/*
		 * 1. General insert
		 *  There is no existsAction check because it is not possible to
		 *  differenciate between two actions by other means than with the id
		 *  and the id is not available
		 */
		$sql = 'INSERT INTO BAction
					(bId, execRank, inputParentRegexGroup, type, useMatchAll, isOpt, title)
					VALUES (:blueprintId, :execRank, :inputParentRegexGroup, :type, :useMatchAll, :isOpt, :title)';
		$this->insertUpdateData($sql, array(
		    ':blueprintId'           => $action->getBlueprint()->getId(),
			':execRank'              => $action->getRank(),
			':inputParentRegexGroup' => $action->getInputParentRegexGroup(),
			':type'                  => $action->getType(),
			':useMatchAll'           => (integer) (($action instanceof ExtractSavable)? $action->getUseMatchAll() : 0),
			':isOpt'                 => (integer) $action->getIsOptional(),
			':title'                 => $action->getTitle() // (($action->hasTitle())? $action->getTitle() : '') Optional title
		));
		$id = $this->getAdapter()->lastInsertId();
		//save the id in the object to make it available to childs
		$action->setId($id);
	}
	
	/**
	 * 
	 * @param Blueprint_Action_Blueprint_Abstract $action
	 * @return unknown_type
	 */
	public function save(AbstractSavable $action)
	{
		//is it intended to be the root action?
		if (!$action->hasParent()) {
			//make sure it can be root action
			if ($action->getType() !== Blueprint::ACTION_TYPE_GETCONTENTS) {
				throw new Exception('Only actions of type Blueprint::ACTION_TYPE_GETCONTENTS can be root');
			}
			
			//ensure the blueprint has not already a root action
			if ($this->existsAnyActionForBlueprint($action->getBlueprint()->getId())) {
				throw new Exception('No parentActionId given, means this is intended to be the root action, prblem is : blueprint has already a root action, to solve this, pass a parentActionId. You can also delete the blueprint action set');
			}
			//now id is available
			$this->insertActionAndSetId($action);
			//the root actions parent is itself
			$parentId = $action->getId();
		} else {
			$this->insertActionAndSetId($action);
			//Savable_Savable has saved all parent instances so now we can access the parent id without fear to crash
			$parentId = $action->getParent()->getId();
		}
		
		if ($action->injectsAction()) {
			$this->saveInjection($action);
		}
		
		/*
		 * 2. Save kinship
		 */
		$this->saveActionKinship($action->getId(), $parentId);
		/*
		 * 3. particular insert only if 'type' === Blueprint::ACTION_TYPE_EXTRACT and root
		 */
		if ($action->getType() === Blueprint::ACTION_TYPE_EXTRACT
		 || $action->isRoot()) {
			if (!$action->hasData()) {
				throw new Exception('When the action is of type Blueprint::ACTION_TYPE_EXTRACT or it is the root, you must call setData(), given: ' . print_r($actionData, true));
			}
			$sql = 'INSERT INTO BAction_Data
						(bActionId, data)
						VALUES (:actionId, :data)';
			$this->insertUpdateData($sql, array(':actionId' => $action->getId(), 
												':data' 	=> (string) $action->getData()));
		}
		
		/*
		 * 3. save group result mapping (group to entity && group to method)
		 */
		if ($action->getType() === Blueprint::ACTION_TYPE_EXTRACT
		 && $action->hasGroupResultMapping()) {
			$this->saveGroupResultMapping($action);
		}
		
		/*
		 * 4.1 particular insert only for type GetContents for setting callback
		 */
		if ($action->getType() === Blueprint::ACTION_TYPE_GETCONTENTS
		 && $action->hasCallable()) {
		 	$this->saveCallback($action);
		}

		/*
		 * 5. Update the Blueprint NewInstanceStartingPointActionId if available
		 */
		if ($action->isNewInstanceGeneratingPoint()) {
			Registry::getInstance($action->getBlueprint())->updateBlueprintNewInstanceGeneratingPointActionId($action->getBlueprint()->getId(), $action->getId());
		}
	}
	
	/**
	 * 
	 * @param unknown_type $action
	 * @return unknown_type
	 */
	private function saveInjection(AbstractSavable $action)
	{
        $injectedAction = $action->getInjectedAction();
		$this->insertUpdateData("INSERT INTO BAction_r_InjectedBAction (bActionId, injectedActionId, inputGroup) VALUES (:id,:iId,:group)", 
								array(':id'    => $action->getId(),
									  ':iId'   => $injectedAction->getId(),
                                      ':group' => (($injectedAction->hasInjectInputGroup())
                                                      ? $injectedAction->getInjectInputGroup() 
                                                      : 0)
                                ));
	}

	/**
	 * 
	 * @param $a
	 * @return unknown_type
	 */
	private function saveGroupResultMapping(ExtractSavable $a)
	{
		if ($a->getGroupResultMapping()->hasGroupToEntityMap()) {
			$this->setActionGroupToEntityMapping($a->getGroupResultMapping()->getGroupToEntityMap(), $a->getId());
		}
		if ($a->getGroupResultMapping()->hasGroupToMethodMap()) {
			$this->setActionGroupToCallableMapping($a->getGroupResultMapping()->getGroupToMethodMap(), $a->getId(), $a->getBlueprint()->getId());
		}
	}
	
	/**
	 * 
	 * @param $a
	 * @return unknown_type
	 */
	private function saveCallback(GetContentsSavable $a)
	{
		$this->saveCallable($a->getCallable());
		if ($a->hasCallbackMap()) {
			$this->saveCallbackMapping($a->getCallbackMap(), $a->getId());
		}
	}
	
	/**
	 * 
	 * @param unknown_type $actionId
	 * @param unknown_type $data
	 * @return unknown_type
	 */
	public function updateActionInputData($actionId, $data)
	{
		$this->checkInput($actionId, $data);
		$this->insertUpdateData("UPDATE BAction_Data SET data = :data WHERE bActionId = :id", array(':data' => $data, ':id' => $actionId));
	}
	
	/**
	 * 
	 * @param unknown_type $actionId
	 * @param unknown_type $data
	 * @return unknown_type
	 */
	private function checkInput($actionId, $data = null)
	{
		if (null !== $data && !is_string($data)) {
			throw new Exception("data must be string, given : " . print_r($data, true));
		}
		if (false === $this->existsAction($actionId)) {
			throw new Exception("action with id : $actionId, does not exit");
		}
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function saveNIGPLastInputData($actionId, $data, $errorTriggerActionId)
	{
		$this->checkInput($actionId, $data);
		$a = array(':data' => $data, ':id' => $actionId, ':eId' => $errorTriggerActionId);
		if (false === $this->getNIGPLastInputData($actionId)) {
			$this->insertUpdateData("INSERT INTO BAction_ErrorData (bNIGPActionId, nIGPLastInputData, errorTriggerActionId) VALUES (:id,:data,:eId)", $a);
		} else {
			$this->insertUpdateData("UPDATE BAction_ErrorData SET nIGPLastInputData = :data, errorTriggerActionId = :eId WHERE bNIGPActionId = :id", $a);
		}
	}
	
	/**
	 * 
	 * @param unknown_type $actionId
	 * @return unknown_type
	 */
	public function getNIGPLastInputData($actionId)
	{
		return $this->getResultSet("SELECT b.bNIGPActionId AS nIGPActionId,
										   b.nIGPLastInputData AS data,
										   b.errorTriggerActionId AS errorActionId 
									FROM BAction_ErrorData AS b
									WHERE b.bNIGPActionId = :id", array(':id' => $actionId));
	}
	
	/**
	 * 
	 * @param unknown_type $id
	 * @param unknown_type $parentId
	 * @return unknown_type
	 */
	private function saveActionKinship($id, $parentId)
	{
		if (!($this->existsAction($id) && $this->existsAction($parentId))) {
			throw new Exception("At least one of the actions with id's: $id, $parentId do not exist.");
		}
		$sql = 'UPDATE BAction SET bParentActionId = :parentActionId WHERE bActionId = :actionId';
		$this->insertUpdateData($sql, array(':actionId' => (integer) $id, ':parentActionId' => (integer) $parentId));
	}
	
	/**
	 * Tells whether there is any action for the given blue print id
	 * 
	 * @return bolean
	 */
	private function existsAnyActionForBlueprint($blueprintId)
	{
		$sql = "SELECT a.bActionId AS actionId
					FROM BAction AS a
					WHERE a.bId = :bId";
		return (boolean) $this->getResultSet($sql, array(':bId' => (integer) $blueprintId));
	}
}
