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
    
    /**
     * 
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }
    
    /**
     * Outdated 
     * @param EventManagerInterface $events
     * @throws Exception
     */
    public function attach(EventManagerInterface $events)
    {
        $identifiers = $events->getIdentifiers();
        if (in_array('Gbili\Miner\Application\Thread', $identifiers)) {
            //$this->listeners[] = $events->attach('placeSameAction.post', array($this->application, 'moreResults'));
        }
    }
}
