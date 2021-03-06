<?php
namespace Gbili\Miner\Gauge;

use Gbili\Stdlib\Gauge\StopPropagation\StopPropagationPositiveGauge;

/**
 * 
 * @author g
 *
 */
class ExecutionAllowedFailsGauge extends StopPropagationPositiveGauge
{
    /**
     * 
     * @param unknown_type $initialCount
     */
    public function __construct($initialCount)
    {
        parent::__construct($initialCount, $initialCount);
    }
}