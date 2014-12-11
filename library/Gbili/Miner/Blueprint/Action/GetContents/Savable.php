<?php
namespace Gbili\Miner\Blueprint\Action\GetContents;

/**
 * This class is meant for saving configuration
 * as opposed to Gbili\Miner\Blueprint\Action\GetContents\Contents\Savable
 * which is in turn used to save the actual data that the 
 * action retrieves from the web when it is exectued
 *
 * @author gui
 *
 */
class Savable
extends \Gbili\Miner\Blueprint\Action\Savable\AbstractSavable
{
	/**
	 * 
	 * @return unknown_type
	 */	
	public function __construct()
	{
		parent::__construct();
		$this->setElement('type', \Gbili\Miner\Blueprint::ACTION_TYPE_GETCONTENTS);
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
