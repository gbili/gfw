<?php
namespace Gbili\Miner\Application;

use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateTrait;

/**
 * 
 * Binds the application to thread events
 * @author g
 *
 */
class ApplicationListenerAggregate implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;
    
    /**
     * 
     * @var \Gbili\Miner\Application\Application
     */
    protected $application = null;
    
    protected $listenerAggregateAttacher;

    /**
     * 
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
        $this->listenerAggregateAttacher = new \Gbili\ListenerAggregateAttacher;
    }
    
    /**
     * Outdated 
     * @param EventManagerInterface $events
     * @throws Exception
     */
    public function attach(EventManagerInterface $events)
    {
        $listeners = [];
        $listeners['Gbili\Miner\Application\Thread'] = array('placeSameAction.post', array($this->application, 'moreResults'));
        $listeners['Gbili\Miner\Gauge\ResultsPerActionGauge'] = array('reachedLimit', array($this->application, 'moreResults'));

        foreach ($this->listenerAggregateAttacher->attachListenersByEventsIdentifier($events, $listeners) as $listener) {
            $this->listeners[] = $listener;
        }
    }
}
