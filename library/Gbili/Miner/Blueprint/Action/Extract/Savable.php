<?php
namespace Gbili\Miner\Blueprint\Action\Extract;

use Gbili\Miner\Blueprint\Action\Savable\AbstractSavable,
    Gbili\Miner\Blueprint\Action\Savable\Exception,
    Gbili\Miner\Blueprint;

/**
 * This class is not meant for any great work, just to ensure
 * that the action gets all its data. And that it gets saved properly
 * 
 * @author gui
 *
 */
class Savable
extends AbstractSavable
{
	/**
	 * 
	 * @var unknown_type
	 */
	const NO_INTERCEPT_METHOD = 0;
	
	/**
	 * 
	 * @param unknown_type $bId
	 * @param unknown_type $parentId
	 * @param unknown_type $data
	 * @return unknown_type
	 */
	public function __construct()
	{
		parent::__construct();
		//set the type on construction forced by parent
		$this->setElement('type', (integer) Blueprint::ACTION_TYPE_EXTRACT);
	}
	
	/**
	 * 
	 * @param unknown_type $bool
	 * @return \Gbili\Miner\Blueprint\Action\Extract\Savable
	 */
	public function setUseMatchAll($bool)
	{
		$this->setElement('useMatchAll', $bool);
		return $this;
	}

	/**
	 * 
	 * @return unknown_type
	 */
	public function getUseMatchAll()
	{
		return $this->getElement('useMatchAll');
	}
	
	/**
	 * 
	 * @param GroupResultMapping $b
	 * @return \Gbili\Miner\Blueprint\Action\Extract\Savable
	 */
	public function setGroupResultMapping(GroupResultMapping $b)
	{
		$this->setElement('groupResultMapping', $b);
		return $this;
	}
	
	/**
	 * Autoset
	 * 
	 * @return unknown_type
	 */
	public function getGroupResultMapping()
	{
		if (!$this->hasGroupResultMapping()) {
			$this->setGroupResultMapping(new GroupResultMapping());
		}
		return $this->getElement('groupResultMapping');
	}

	/**
	 * 
	 * @return unknown_type
	 */
	public function hasGroupResultMapping()
	{
		return $this->isSetKey('groupResultMapping');
	}

	/**
	 * Proxy + added functionality (allow to set entity and method intercept at same time)
	 * @param unknown_type $group
	 * @param unknown_type $entity
	 * @param unknown_type $param3
	 * @param unknown_type $param4
	 * @throws Exception
	 * @return \Gbili\Miner\Blueprint\Action\Extract\Savable
	 */
	public function spitGroupAsEntity($group, $entity, $param3 = false, $param4 = self::NO_INTERCEPT_METHOD)
	{
		//user wants to use param3 as isOptional and param4 as $resultInterceptMethod
		if (is_bool($param3)) {
			$isOptional = $param3;
			$resultInterceptMethod = (is_string($param4))? $param4 : self::NO_INTERCEPT_METHOD;
		//user used 3 paramter as resultInterceptMethod, and wants the 4th param to be isOptional default value
		} else if (is_string($param3)) {
			$isOptional = (is_bool($param4))? $param4 : false;
			$resultInterceptMethod = $param3;
		} else {
			throw new Exception('3 and 4 Parameter values not supported');
		}

		//set mapping to entity
		$this->getGroupResultMapping()->spitGroupAsEntity($group, $entity, $isOptional);
		
		//allow the result to be intercepted before spitting,
		//this can also be done by calling $this->interceptGroupsOneByOne
		if (self::NO_INTERCEPT_METHOD !== $resultInterceptMethod) {
			if (!is_string($resultInterceptMethod)) {
				throw new Exception('$resultInterceptMethod (4th param) must be a string');
			}
			$this->getGroupResultMapping()->interceptGroupsOneByOne($group, $resultInterceptMethod);
		}
		return $this;
	}

	/**
	 * Proxy
	 * 
	 * @param unknown_type $groups
	 * @param unknown_type $methodName
	 * @return \Gbili\Miner\Blueprint\Action\Extract\Savable
	 */
	public function interceptGroupsOneByOne($groups, $methodName)
	{
		$this->getGroupResultMapping()->interceptGroupsOneByOne($groups, $methodName);
		return $this;
	}

	/**
	 * Proxy
	 * 
	 * @param array $groups
	 * @param unknown_type $methodName
	 * @return \Gbili\Miner\Blueprint\Action\Extract\Savable
	 */
	public function interceptGroupsTogether(array $groups, $methodName)
	{
		$this->getGroupResultMapping()->interceptGroupsTogether($groups, $methodName);
		return $this;
	}
}
