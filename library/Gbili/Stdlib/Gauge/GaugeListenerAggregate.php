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
    
    /**
     * 
     * @param number $max
     */
    public function __construct(Gauge $gauge)
    {
        $this->gauge = $gauge;
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
        $this->attachements[$eventsIndentifier][] = array($event, $callbackName, $priority);
    }

    /**
     * 
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $eventsAwareClass = $events->getIdentifiers();
        $listenToEventsIdentifier = end($eventsAwareClass);
        $possibleEventsIndentifiers = array_keys($this->attachements);
        if (!in_array($listenToEventsIdentifier, $possibleEventsIndentifiers)) {
            throw new \Exception('The event manager must pertain to one of : ' . print_r($possibleEventsIndentifiers, true) . ', currently it pertains to :' . print_r($eventsAwareClass, true));
        }
        
        foreach ($this->attachements[$listenToEventsIdentifier] as $attachement) {
            list($event, $callbackName, $priority) = $attachement;
    	    $this->listeners[] = $events->attach($event, array($this->gauge, $callbackName), $priority);
        }
    }
}
