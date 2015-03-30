<?php
namespace Gbili\Miner\Blueprint\Db;

use Gbili\Url\Authority\Host;

interface DbInterface
{
	/**
	 * 
	 * @param Host $host
	 * @return array : 'newInstanceGeneratingPointActionId',
	 * 				   'path',
	 * 				   'pathType',
	 * 				   'classType'
	 */
	public function getBlueprintInfo(Host $host);
	
	/**
	 * 
	 * @param string | integer $injectedActionId
	 * @return array : 'actionId',
	 * 				   'inputGroup'
	 */
	public function getInjectionData($injectedActionId);
	
	/**
	 * 
	 * @param unknown_type $actionId
	 * @return array : 'regexGroup',
	 * 				   'entity',
	 * 				   'isOpt'
	 */
	public function getActionGroupToEntityMapping($actionId);
	
	/**
	 * 
	 * @param Host $host
	 * @return array : 'actionId',
	 * 				   'parentId',
	 * 				   'inputGroup',
	 * 				   'type',
	 * 				   'useMatchAll'
	 * 				   'isOpt',
	 * 				   'title',
	 * 				   'data'
	 */
	public function getActionSet(Host $host);
	
	/**
	 * 
	 * @param unknown_type $actionId
	 * @return array : 'methodName'
	 */
	public function getActionCallable($actionId);
	
	/**
	 * 
	 * @param unknown_type $actionId
	 * @return array : 'regexGroup',
	 * 				   'paramNum'
	 */
	public function getActionCallableParamsToGroupMapping($actionId);
	
	/**
	 * 
	 * @param unknown_type $actionId
	 * @return array : 'methodName',
	 * 				   'regexGroup',
	 * 				   'interceptType'
	 */
	public function getActionGroupToCallableAndInterceptType($actionId);
}
