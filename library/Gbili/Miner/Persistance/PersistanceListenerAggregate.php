<?php
namespace Gbili\Miner\Persistance;
use Gbili\Miner\Persistance;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\EventManager\ListenerAggregateInterface;

class PersistanceListenerAggregate implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;
    
    /**
     * 
     * @var \Gbili\Miner\Lexer\AbstractLexer
     */ protected $engine = null;
    
    /**
     * 
     * @param AbstractLexer $lexer
     */
    public function __construct(Persistance $engine)
    {
        $this->engine = $engine;
    }
    
    /**
     * (non-PHPdoc)
     * @see Zend\EventManager.ListenerAggregateInterface::attach()
     */
	public function attach(EventManagerInterface $events)
	{
        $eventsAwareClass = $events->getIdentifiers();
        if (in_array('Gbili\Miner\Application\Thread', $eventsAwareClass)) {
    	    $this->listeners[] = $events->attach('setAction.isNewInstanceGeneratingPoint', array($this->engine, 'addPersistableInstance'));
        } else if (in_array('Gbili\Miner\Gauge\UnpersistedInstancesGauge', $eventsAwareClass)) {
    	    $this->listeners[] = $events->attach('onReachedMax', array($this->engine, 'persistInstances'));
        } else if (in_array('Gbili\Miner\Gauge\PersistanceAllowedFailsGauge', $eventsAwareClass)) {
            $this->listeners[] = $events->attach('onReachedMax', array($this->engine, 'terminate'));
        } else {
            throw new \Exception('The event manager is not supported by ' . __CLASS__ . ':' . print_r($eventsAwareClass, true));
        }
	}
}
