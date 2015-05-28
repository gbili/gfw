<?php
namespace Gbili\Stdlib\Gauge;

use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventInterface;
use Zend\EventManager\ListenerAggregateTrait;

/**
 * 
 * @author g
 *
 */
class GaugeListenerAggregate implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;
    
    protected $attachements = array();
    
    protected $gauge;
    
    protected $listenerAggregateAttacher;

    /**
     * 
     * @param number $max
     */
    public function __construct(Gauge $gauge)
    {
        $this->gauge = $gauge;
        $this->listenerAggregateAttacher = new \Gbili\ListenerAggregateAttacher;
    }
    
    /**
     * To which events is this listener going to be listening
     * @param string $eventsIndentifier whose eventmanager is going to be passed to attach
     * @param string $event
     * @param mixed $callback
     * @param integer $priority
     */
    public function addAttachement($eventsIndentifier, $event, $callbackName, $priority = 1)
    {
        if (!isset($this->attachements[$eventsIndentifier])) {
            $this->attachements[$eventsIndentifier] = array();
        }
        $this->attachements[$eventsIndentifier][] = array($event, array($this->gauge, $callbackName), $priority);
    }

    /**
     * 
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        foreach ($this->listenerAggregateAttacher->attachListenersByEventsIdentifier($events, $this->attachements) as $listener) {
            $this->listeners[] = $listener;
        }
    }
}
