<?php
namespace Gbili\Miner\Lexer;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\EventManager\ListenerAggregateInterface;

/**
 * Binds the Lexer to application events
 * @author g
 */
class LexerListenerAggregate implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;
    
    /**
     * 
     * @var \Gbili\Miner\Lexer\AbstractLexer
     */
    protected $lexer;

    protected $listenerAggregateAttacher;
    
    /**
     * 
     * @param AbstractLexer $lexer
     */
    public function __construct(AbstractLexer $lexer)
    {
        $this->lexer = $lexer;
        $this->listenerAggregateAttacher = new \Gbili\ListenerAggregateAttacher;
    }
    
    /**
     * (non-PHPdoc)
     * @see Zend\EventManager.ListenerAggregateInterface::attach()
     */
	public function attach(EventManagerInterface $events)
	{
        $listeners = [];
        $listeners['Gbili\Miner\Application\Application'] = ['manageExecutedAction.hasFinalResults', [$this, 'manageData']];
        $listeners['Gbili\Miner\Persistance'] = ['addPersistableInstance.post', [$this, 'setPopulableInstance']];

        foreach ($this->listenerAggregateAttacher->attachListenersByEventsIdentifier($events, $listeners) as $listener) {
            $this->listeners[] = $listener;
        }
	}
	
	/**
	 * 
	 * @param EventInterface $e
	 */
	public function setPopulableInstance(EventInterface $e)
	{
	    $this->lexer->setPopulableInstance($e->getParam('persistableInstance'));
	}
	
	/**
	 * 
	 * @param EventInterface $e
	 */
	public function manageData(EventInterface $e)
	{
	    $this->lexer->manageData($e->getParam('results'));
	}
}
