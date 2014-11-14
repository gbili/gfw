<?php
namespace Gbili\Stdlib\Gauge;

/**
 * 
 * @author g
 *
 */
class Gauge implements ReachedLimitInterface
{   
    /**
     * 
     * @var number
     */
    protected $initialCount = null;
    
    /**
     * 
     * @var number
     */
    protected $count = null;
    
    /**
     * 
     * @var number
     */
    protected $max = null;
    
    /**
     * Initial count is used as max, can override that with setMax($num)
     * or removeMax() for inifinite max
     * 
     * @param integer $initialCount
     */
    public function __construct($initialCount = 0, $max = null)
    {
        $this->checkZeroCompatibility($initialCount);
        $this->initialCount = (integer) $initialCount;
        $this->setMax((null !== $max)? $max : $this->initialCount);
        return $this;
    }
    
    /**
     * 
     * @return integer
     */
    public function getCount()
    {
        return $this->count;
    }
    
    /**
     * @return integer
     */
    public function getInitialCount()
    {
        return $this->initialCount;
    }
    
    /**
     * 
     * @return integer
     */
    public function getMax()
    {
        return $this->max;
    }
    
    /**
     * Only add if max was not reached
     *
     * @return boolean Was the add added: true. Reached ceil already: false
     */
    public function add($number = 1)
    {
        if ($this->reachedMax()) {
            return false;
        }
    
        if (1 !== $number && !is_numeric($number)) {
            throw new Exception('Must be numeric');
        }
    
        $this->count += (integer) $number;
        return true;
    }
    
    /**
     * If it has addes, subtract, otherwise throw
     * user should have checked the return value
     * 
     * @return boolean was the subtract added
     */
    public function subtract($number = 1)
    {
        if (!$this->isPositive()) {
            return false;
        }
        
        if (1 !== $number && !is_numeric($number)) {
            throw new Exception('Must be numeric');
        }
        
        $this->count -= (integer) $number;
        return true;
    }
    
    /**
     * Override this if you want different
     * behaviour than onReachedZero()
     * @see onRemoveMax()
     */
    public function onNegativeInitialCount()
    {
        throw new Exception('Cannot set a negative initial count');
    }
    
    /**
     * Override this if you want a different
     * behaviour than onReachedMax()
     * Here we call this, to allow max bound
     * subclasses to allow to throw if
     * max is being removed, which would not
     * make sense since their only purpose is
     * to monitor max...
     * 
     * @return boolean
     */
    public function onRemoveMax()
    {
        throw new Exception('Cannot remove max if not in positive mode');
    }
    
    /**
     * Never reaches max if max is 0
     * 
     */
    public function reachedMax()
    {
        return (0 !== $this->max) && ($this->max <= $this->count);
    }
    
    /**
     * 
     * @return boolean
     */
    public function isPositive()
    {
        return (0 < $this->count);
    }
    
    /**
     * 
     * @param integer $max
     */
    public function setMax($max)
    {
        $this->checkMaxCompatibility($max);
        $this->max = (integer) $max;
        $this->resetCount();
    }
    
    /**
     * 
     * @param number $max
     * @throws Exception
     */
    protected function checkMaxCompatibility($max)
    {
        if (0 === $max) {
            $this->onRemoveMax();
        } else if ($this->initialCount > $max) {
            throw new Exception('You cannot set a max lower than the initalCount when isThrowOnReachedMax(), it is burst before it starts.');
        }
    }
    
    /**
     * 
     * @param number $initalCount
     * @throws Exception
     */
    protected function checkZeroCompatibility($initialCount)
    {
        if (0 > $initialCount) {
            $this->onNegativeInitialCount();
        }
    }
    
    /**
     * 
     */
    public function removeMax()
    {
        $this->setMax(0);
    }
    
    /**
     * 
     */
    public function resetCount()
    {
        $this->count = $this->initialCount;
    }
    
    /**
     * 
     * @return boolean
     */
    public function reachedLimit()
    {
        return !$this->isPositive() || $this->reachedMax();
    }
}