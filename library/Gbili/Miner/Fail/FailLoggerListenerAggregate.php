<?php
namespace Gbili\Miner\Fail;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\EventManager\ListenerAggregateInterface;

class FailLoggerListenerAggregate implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;
    
    /**
     * 
     * @var \Gbili\Miner\Lexer\AbstractLexer
     */
    protected $failLogger = null;
    
    /**
     * 
     * @param AbstractLexer $lexer
     */
    public function __construct(FailLogger $failLogger)
    {
        $this->failLogger = $failLogger;
    }
    
    /**
     * (non-PHPdoc)
     * @see Zend\EventManager.ListenerAggregateInterface::attach()
     */
	public function attach(EventManagerInterface $events)
	{
	    $eventAwareClassIdentifiers = $events->getIdentifiers();
        $eventsAwareClass = end($eventAwareClassIdentifiers);
        switch ($eventsAwareClass) {
            case 'Gbili\Miner\Application\Application':
        	    $this->listeners[] = $events->attach('executeAction.fail', array($this->failLogger, 'logExecutionFail'), 1001);
        	    break;
            case 'Gbili\Miner\Persistance':
        	    $this->listeners[] = $events->attach('persistInstance.fail', array($this->failLogger, 'logPersitanceFail'), 1001);
                break;
            default:
                throw new Exception('EventManager not supported by:' . __CLASS__ . ' instance of :' . $eventsAwareClass . ' not expected');
                break;
        }
	}
}
