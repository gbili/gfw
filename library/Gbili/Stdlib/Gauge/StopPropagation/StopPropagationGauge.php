<?php
namespace Gbili\Stdlib\Gauge\StopPropagation;

use Gbili\Stdlib\Gauge\Gauge;

class StopPropagationGauge extends Gauge
{
    /**
     * (non-PHPdoc)
     * @see Gbili\Stdlib\Gauge.Gauge::subtract()
     */
    public function subtract($event = 1)
    {
        return $this->operation(__FUNCTION__, $event);
    }
    
    /**
     * (non-PHPdoc)
     * @see Gbili\Stdlib\Gauge.Gauge::add()
     */
    public function add($event = 1)
    {
        return $this->operation(__FUNCTION__, $event);
    }
    
    /**
     * 
     * @param unknown_type $type
     * @param Zend\EventManager\EventInterface $e
     */
    protected function operation($type, \Zend\EventManager\EventInterface $e)
    {
        if (!$number = $e->getParam('number', false)) {
            $ret = parent::{$type}();
        } else {
            $ret = parent::{$type}($number);
        }
        if ($this->reachedLimit()) {
            $e->stopPropagation();
        }
    }
}
