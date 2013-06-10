<?php
namespace Gbili\Stdlib\Gauge\StopPropagation;

//use Gbili\Stdlib\Gauge\PositiveGaugeTrait;

class StopPropagationPositiveGauge extends StopPropagationGauge
{
    // --------------------- Replaceable with PositiveGaugeTrait--------------------
    /**
     * 
     * @param integer $initialCount
     */
    public function __construct($initialCount = 5, $max = 5)
    {
        return parent::__construct($initialCount, $max);
    }
    
    /**
     * 
     * @throws Exception
     */
    public function onNegativeInitialCount()
    {
        throw new Exception('Inital count cannot be <= 0 when in StricPositive, it is burst before it starts');
    }
    
    /**
     * 
     * @return boolean
     */
    public function reachedLimit()
    {
        return !$this->isPositive();
    }
    // --------------------- Replaceable with PositiveGaugeTrait--------------------
}