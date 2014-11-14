<?php
namespace Gbili\Stdlib\Gauge\Events;

use Gbili\Stdlib\Gauge\Gauge;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\EventManager\EventInterface;

class EventsGauge extends Gauge implements EventManagerAwareInterface
{
    use EventManagerAwareTrait;
    
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
     * @param EventInterface $e
     */
    protected function operation($type, EventInterface $e)
    {
        $this->getEventManager()->trigger($type . '.pre', $this, $this->getParams());
        if (!$number = $e->getParam('number', false)) {
            $ret = parent::{$type}();
        } else {
            $ret = parent::{$type}($number);
        }
        $this->getEventManager()->trigger($type . '.post', $this, $this->getParams());
        
        if ($this->reachedLimit()) {
            $this->getEventManager()->trigger('reachedLimit', $this, $this->getParams());
        }
        return $ret;
    }
    
    /**
     * 
     * @return multitype:NULL
     */
    protected function getParams()
    {
        return array(
            'max' => $this->getMax(),
            'count' => $this->getCount(),
            'initalCount' => $this->getInitialCount()
        );
    }
}
