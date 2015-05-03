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

    protected $listenerAggregateAttacher;
    
    /**
     * 
     * @param AbstractLexer $lexer
     */
    public function __construct(Fail $fail)
    {
        $this->fail = $fail;
        $this->listenerAggregateAttacher = new \Gbili\ListenerAggregateAttacher;
    }
    
    /**
     * (non-PHPdoc)
     * @see Zend\EventManager.ListenerAggregateInterface::attach()
     */
	public function attach(EventManagerInterface $events)
	{
        $listeners = [];
        $listeners['Gbili\Miner\Application\Application'] = ['executeAction.fail', [$this->fail, 'manageExecutionFail']];
        $listeners['Gbili\Miner\Persistance'] = ['persistInstance.fail', [$this->fail, 'managePersitanceFail']];

        foreach ($this->listenerAggregateAttacher->attachListenersByEventsIdentifier($events, $listeners) as $listener) {
            $this->listeners[] = $listener;
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
