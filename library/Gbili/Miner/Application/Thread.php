<?php
namespace Gbili\Miner\Application;

use Gbili\Miner\Blueprint\Action\AbstractAction;
use Gbili\Miner\Application\Application;
use Gbili\Miner\Blueprint;
use Gbili\Miner\AttachableListenersInterface;

use Zend\EventManager\EventManagerAwareTrait;
use Zend\EventManager\EventManagerAwareInterface;

/**
 * This class will hold the current
 * action being executed 
 * 
 * @author gui
 *
 */
class Thread implements EventManagerAwareInterface, AttachableListenersInterface
{
    use EventManagerAwareTrait;
    
    /**
     * 
     * @var unknown_type
     */
    protected $defaultListeners = array(
        'PersistanceListenerAggregate',      // Listen for new instance generation
        //'ResultsPerActionGaugeListenerAggregate', specified in constructor optionally// Monitor how many results an action can execute
    );
    
	/**
	 * The action being executed
	 * 
	 * @var \Gbili\Miner\Blueprint\Action\AbstractAction
	 */
	protected $action = null;
	
	/**
	 * Don't need to keep the blueprint,
	 * since every action has a reference to it
	 * 
	 * @return void
	 */
	public function __construct(Blueprint $blueprint)
	{ 
		$this->action    = $blueprint->getRoot();
		
        if (isset($config['limited_results_action_id'])) {
            $this->defaultListeners[] = 'ResultsPerActionGaugeListenerAggregate';
        }
	}
	
    /**
     * 
     */
    public function getListeners()
    {
        return $this->defaultListeners;
    }
    
	/**
	 * 
	 * @return \Gbili\Miner\Blueprint\Action\AbstractAction
	 */
	public function getAction()
	{
		if (null === $this->action) {
			throw new Exception("The action is not set");
		}
		return $this->action;
	}
	
	/**
	 * 
	 * @param \Gbili\Miner\Blueprint\Action\AbstractAction $action
	 * @return void
	 */
	public function setAction(AbstractAction $action)
	{
        if ($action->isNewInstanceGeneratingPoint()) {
            $this->getEventManager()->trigger(__FUNCTION__ . '.isNewInstanceGeneratingPoint', array('thread', $this));
        }
		$this->action = $action;
	}
	
	/**
	 * 
	 */
	public function retakeFlowFromParent()
	{
        $this->getAction()->clear();
	    $this->setAction($this->getAction()->getParent());
	    $this->getEventManager()->trigger(__FUNCTION__ . '.post', array('thread', $this));
	}
	
	/**
	 * 
	 */
	public function placeChildIntoFlow()
	{
	    $this->setAction($this->getAction()->getChild());
	    $this->getEventManager()->trigger(__FUNCTION__ . '.post', array('thread', $this));
	}
	
	/**
	 * This means moreResults, so next result on the same action
	 */
	public function placeSameAction()
	{
	    $this->setAction($this->getAction());
	    $this->getEventManager()->trigger(__FUNCTION__ . '.action' . $this->getAction()->getId(), array('thread', $this));
	}
}
