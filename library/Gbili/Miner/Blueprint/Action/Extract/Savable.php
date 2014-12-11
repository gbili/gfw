<?php
namespace Gbili\Miner\Blueprint\Action\Extract;

/**
 * This class is not meant for any great work, just to ensure
 * that the action gets all its data. And that it gets saved properly
 * 
 * @author gui
 *
 */
class Savable
extends \Gbili\Miner\Blueprint\Action\Savable\AbstractSavable
{
	/**
	 * 
	 * @return void 
	 */
	public function __construct()
	{
		parent::__construct();
		//set the type on construction forced by parent
		$this->setElement('type', (integer) \Gbili\Miner\Blueprint::ACTION_TYPE_EXTRACT);
	}
	
	/**
	 * 
	 * @param boolean $bool
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
     * Delegate to group result Mapping
     */
    public function __call($method, $args)
    {
        if (!method_exists($this->getGroupResultMapping(), $method) {
            throw new Exception("The method: $method does not exist");
        }
        return call_user_func_array(array($this->getGroupResultMapping(), $method()), $args);
    }
}
