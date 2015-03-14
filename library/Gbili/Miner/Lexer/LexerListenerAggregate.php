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
    protected $lexer = null;
    
    /**
     * 
     * @param AbstractLexer $lexer
     */
    public function __construct(AbstractLexer $lexer)
    {
        $this->lexer = $lexer;
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
        	    $this->listeners[] = $events->attach('manageExecutedAction.hasFinalResults', array($this, 'manageData'));
        	    break;
            case 'Gbili\Miner\Persistance':
        	    $this->listeners[] = $events->attach('addPersistableInstance.post', array($this, 'setPopulableInstance'));
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
