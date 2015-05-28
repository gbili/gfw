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
    
    protected $listenerAggregateAttacher;

    /**
     * 
     * @param AbstractLexer $lexer
     */
    public function __construct(FailLogger $failLogger)
    {
        $this->failLogger = $failLogger;
        $this->listenerAggregateAttacher = new \Gbili\ListenerAggregateAttacher;
    }
    
    /**
     * (non-PHPdoc)
     * @see Zend\EventManager.ListenerAggregateInterface::attach()
     */
	public function attach(EventManagerInterface $events)
	{
        $listeners = [];
        $listeners['Gbili\Miner\Application\Application'] = ['executeAction.fail', array($this->failLogger, 'logExecutionFail'), 1001];
        $listeners['Gbili\Miner\Persistance'] = ['persistInstance.fail', array($this->failLogger, 'logPersitanceFail'), 1001];

        foreach ($this->listenerAggregateAttacher->attachListenersByEventsIdentifier($events, $listeners) as $listener) {
            $this->listeners[] = $listener;
        }
	}
}
