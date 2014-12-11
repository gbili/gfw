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
     * @return mixed
     */
    public function __call($method, $args)
    {
        $grm = $this->getGroupResultMapping();
        if (!method_exists($grm, $method)) {
            throw new Exception("The method: $method does not exist");
        }
        $ret = call_user_func_array(array($grm, $method), $args);

        // Instead of returning grm return $this 
        // Useful to allow method chaining
        if ($ret === $grm) {
            $ret = $this;
        } 
        return $ret;
    }
}
