<?php
namespace Gbili\Miner\Blueprint\Db;

use Gbili\Db\Req\AbstractReq,
    Gbili\Url\Authority\Host;

class Req
extends AbstractReq
implements DbInterface
{
	/**
	 * 
	 * @return unknown_type
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * This will get the paths an new instance
	 * generating point action id
	 * 
	 * @param Host $host
	 * @return unknown_type
	 */
	public function getBlueprintInfo(Host $host)
	{
		return $this->getResultSet("SELECT b.bId AS bId,
									   b.newInstanceGeneratingPointActionId AS newInstanceGeneratingPointActionId,
									   cm.path AS path,
									   cm.pathType AS pathType,
									   cm.classType AS classType
									FROM Blueprint AS b 
										LEFT JOIN Blueprint_CMPaths AS cm 
											ON (b.bId = cm.bId)
									WHERE b.host = :host",
								  array(':host' => $host->toString()));
	}
	
	/**
	 * 
	 * @param unknown_type $injectedActionId
	 * @return unknown_type
	 */
	public function getInjectionData($injectedActionId)
	{
		return $this->getResultSet("SELECT b.bActionId AS injectingActionId,
										   b.inputGroup AS inputGroup 
									FROM BAction_r_InjectedBAction AS b 
									WHERE b.injectedActionId = :id",
									array(':id' => (integer) $injectedActionId));
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
	public function getActionGroupToEntityMapping($actionId)
	{
		$sql = "SELECT b.regexGroup AS regexGroup, 
					   b.const AS entity,
					   b.isOpt AS isOpt
					FROM BAction_RegexGroup_r_Const AS b
					WHERE b.bActionId = :actionId";
		return $this->getResultSet($sql, array(':actionId' => $actionId));
	}
	
	/**
	 * Returns all the rows in tha Actions table
	 * where the host is the same as specified
	 * in the url
	 * 
	 * @param Host $host
	 * @return unknown_type
	 */
	public function getActionSet(Host $host)
	{
		$sql = "SELECT a.bactionId AS actionId,
					   a.bParentActionId AS parentId,
					   a.inputParentRegexGroup AS inputGroup,
					   a.type AS type,
					   a.useMatchAll AS useMatchAll,
					   a.isOpt AS isOpt,
					   a.title AS title,
					   d.data AS data
					FROM Blueprint AS b 
						INNER JOIN BAction AS a ON (b.bId = a.bId)
						LEFT JOIN BAction_Data AS d ON (a.bActionId = d.bActionId)
					WHERE b.host = :host
					ORDER BY a.execRank ASC";
		return $this->getResultSet($sql, array(':host' => $host->toString()));
	}
	
	/**
	 * 
	 * @param unknown_type $actionId
	 * @return unknown_type
	 */
	public function getActionCallbackMethodName($actionId)
	{
		return $this->getResultSet("SELECT c.methodName AS methodName
										FROM BAction_CallbackMethod AS c 
										WHERE c.bActionId = :bActionId",
									array(':bActionId' => $actionId));
	}
	
	/**
	 * 
	 * @param unknown_type $actionId
	 * @return unknown_type
	 */
	public function getActionCallbackParamsToGroupMapping($actionId)
	{	
		$sql = "SELECT d.regexGroup AS regexGroup,
					   d.paramNum AS paramNum
					FROM BAction_RegexGroup_r_CallbackMethod_ParamNum AS d 
					WHERE d.bActionId = :bActionId 
					ORDER BY d.paramNum ASC";
		return $this->getResultSet($sql, array(':bActionId' => $actionId));
	}
	
	/**
	 * 
	 * @param unknown_type $actionId
	 * @return unknown_type
	 */
	public function getActionGroupToMethodNameAndInterceptType($actionId)
	{
		$sql = "SELECT m.name AS methodName,
					   b.regexGroup AS regexGroup,
					   b.interceptType AS interceptType
					FROM Blueprint_MethodMethod as m
						LEFT JOIN BAction_RegexGroup_r_MethodMethod as b
							ON (m.methodId = b.methodId)
					WHERE b.bActionId = :actionId
					ORDER BY b.interceptType ASC, m.name ASC, b.regexGroup ASC";
		return $this->getResultSet($sql, array(':actionId' => $actionId));
		
	}
}
