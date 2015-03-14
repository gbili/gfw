<?php
namespace Gbili\Miner\Blueprint\Action\GetContents;

use Gbili\Miner\Blueprint\Action\Savable\AbstractSavable, 
    Gbili\Miner\Blueprint\Action\ClassMethodLoader, 
    Gbili\Miner\Blueprint;

/**
 * 
 * @author gui
 *
 */
class Savable
extends AbstractSavable
{
	/**
	 * 
	 * @return unknown_type
	 */	
	public function __construct()
	{
		parent::__construct();
		$this->setElement('type', Blueprint::ACTION_TYPE_GETCONTENTS);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasCallbackMethod()
	{
		return $this->isSetKey('callbackMethod');
	}
	
	/**
	 * 
	 * @param unknown_type $methodName
	 * @throws Exception
	 * @return \Gbili\Miner\Blueprint\Action\GetContents\Savable
	 */
	public function setCallbackMethod($methodName)
	{
		if (!is_string($methodName)) {
			throw new Exception('the method name must be passed as a string');
		}
		if (!$this->hasBlueprint()) {
			throw new Exception('The blueprint must be set in order tu map the action to its callback method');
		}
		if ($this->getBlueprint()->hasCallbackPath()) {
			$path = $this->getBlueprint()->getCallbackPath();
			$type = ClassMethodLoader::PATH_TYPE_DIRECT;
		} else if ($this->getBlueprint()->hasBasePath()) {
			$path = $this->getBlueprint()->getBasePath();
			$type = ClassMethodLoader::PATH_TYPE_BASE;
		} else {
			throw new Exception('There is no way to find the callback class if no path is provided in blueprint');
		}
		if (!is_string(($className = ClassMethodLoader::loadCallbackClass($path, $this->getBlueprint()->getHost(), $type)))) {
			throw new Exception('the class could not be loaded errors : ' . print_r(ClassMethodLoader::getErrors(), true));
		}
		if (false === ClassMethodLoader::methodExists($className, $methodName)) {
			throw new Exception("the method '$methodName' does not exist in $className");
		}
		$this->setElement('callbackMethod', $methodName);
		
		return $this;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getCallbackMethod()
	{
		return $this->getElement('callbackMethod');
	}
	
	/**
	 * 
	 * @param array $mapping
	 * @throws Exception
	 * @return \Gbili\Miner\Blueprint\Action\GetContents\Savable
	 */
	public function setCallbackMap(array $mapping)
	{
		if (array_keys($mapping) !== range(0, count($mapping) - 1)) {
			throw new Exception('Mapping not supported, keys should range from 0 to n');
		}
		$this->setElement('callbackMapping', $mapping);
		
		return $this;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getCallbackMap()
	{
		return $this->getElement('callbackMap');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasCallbackMap()
	{
		if ($ret = $this->isSetKey('callbackMap')) {
			$ret = $this->getElement('callbackMap');
			$ret = !empty($ret);//ensure not empty array
		}
		return $ret;
	}
}
