<?php
namespace Gbili\GafasonlineEs;

use Gbili\GafasonlineEs\Fail;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\EventManager\ListenerAggregateInterface;

class FailListenerAggregate implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;
    
    /**
     * 
     * @var \Gbili\Miner\Lexer\AbstractLexer
     */
    protected $fail = null;
    
    /**
     * 
     * @param AbstractLexer $lexer
     */
    public function __construct(Fail $fail)
    {
        $this->fail = $fail;
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
        	    $this->listeners[] = $events->attach('executeAction.fail', array($this->fail, 'manageExecutionFail'));
        	    break;
            case 'Gbili\Miner\Persistance':
        	    $this->listeners[] = $events->attach('persistInstance.fail', array($this->fail, 'managePersitanceFail'));
                break;
            default:
                throw new Exception('EventManager not supported by:' . __CLASS__ . ' instance of :' . $eventsAwareClass . ' not expected');
                break;
        }
	}
	
	/**
	 * 
	 * @param EventInterface $e
	 */
	public function manageEexecutionFail(EventInterface $e)
	{
	    $this->fail->manageExecutionFail($e->getParam('thread'), $e->getTarget());
	}
	
	/**
	 * 
	 * @param EventInterface $e
	 */
	public function managePersitanceFail(EventInterface $e)
	{
	    $this->fail->managePersistanceFail($e->getParam('persistableInstance'), $e->getParam('exception'), $e->getTarget());
	}
}
