<?php
namespace Gbili\Miner\Blueprint\Action\GetContents;

use Gbili\Miner\Blueprint\Action\Savable\AbstractSavable, 
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
	public function hasCallable()
	{
		return $this->isSetKey('callable');
	}
	
	/**
	 * 
	 * @param mixed:string|array $callable
	 * @throws Exception
	 * @return \Gbili\Miner\Blueprint\Action\GetContents\Savable
	 */
	public function setCallable($callable, $methodName=null)
	{
		if (!$this->hasBlueprint()) {
			throw new Exception('The blueprint must be set in order tu map the action to its callback method');
		}
        if (null !== $methodName) {
            $callable = array($callable, $methodName);
        }
		$this->setElement('callable', $callable);
		
		return $this;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getCallable()
	{
		return $this->getElement('callable');
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
