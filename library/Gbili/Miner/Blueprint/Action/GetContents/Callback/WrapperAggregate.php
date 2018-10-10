<?php
namespace Gbili\Miner\Blueprint\Action\GetContents\Callback;

/**
 * This class serves as a wrapper for the Miner_Persistance_Blueprint_Action_GetContents_Callback_Abstract
 * subclasses.
 * 
 * This instantiates the subclass, sets the input mapping
 * and calls the callback() method and passes it the input
 * 
 * @author gui
 *
 */
class WrapperAggregate
{
    protected $wrappers = array();

    /**
     * @todo add priority ordering
     */
    public function addWrapper(Wrapper $wrapper, $priority)
    {
        $this->wrappers[] = $wrapper;
    }

    public function getWrappers()
    {
        return $this->wrappers;
    }

    public function hasWrappers()
    {
        return !empty($this->wrappers);
    }

	/**
	 * 
	 * @return unknown_type
	 */
	public function apply($input)
	{
        $callbackResult = $input;
        foreach ($this->getWrappers() as $wrapper) {
            $callbackResult = $wrapper->apply($callbackResult);
            if ($wrapper->stopPropagation()) {
                break;
            }
        }
        return $callbackResult;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function rewindLoop()
	{
        foreach ($this->getWrappers() as $wrapper) {
            $wrapper->rewindLoop();
        }
	}
	
	/**
	 * this function will allways return true
	 * if you don't set a value to $this->hasMoreLoops
	 * now it is set when you call apply()
	 * 
	 * @return unknown_type
	 */
	public function hasMoreLoops()
	{
        $answer = false;
        foreach ($this->getWrappers() as $wrapper) {
            $answer = $wrapper->hasMoreLoops() || $answer;
            if ($wrapper->stopPropagation()) {
                break;
            }
        }
        return $answer;
	}
}
