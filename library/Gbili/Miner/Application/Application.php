<?php
namespace Gbili\Miner\Application;

use Gbili\Miner\Service\ServiceManagerConfig;

use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\CallbackHandler;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\EventManager\EventManagerAwareInterface;

use Gbili\Miner\AttachableListenersInterface;
use Gbili\Miner\Application\FlowEvaluator;

/**
 * 
 * @author g
 *
 */
class Application implements EventManagerAwareInterface, AttachableListenersInterface
{
    /**
     * Returned when too many fails have happened 
     *
     * @var 
     */
    const NO_MORE_FAILS_ALLOWED = 4;

    use EventManagerAwareTrait;
    
    /**
     * 
     * @var mixed
     */
    protected $defaultListeners = array(
        'LexerListenerAggregate',  // Listen for mined data spitting
        'ExecutionAllowedFailsGaugeListenerAggregate', //manageFail.normalAction
        'FailLoggerListenerAggregate', //executeAction.fail
    );
    
    /**
     * 
     * @var \Gbili\Miner\Application\Thread
     */
    protected $thread;
    
    /**
     * 
     * @var \Gbili\Miner\Application\FlowEvaluator
     */
    protected $flowEvaluator = null;
    
    
    /**
     * 
     * @param Thread $thread
     */
    public function __construct(Thread $thread, array $config)
    {
        $this->thread = $thread;
        $this->flowEvaluator = new FlowEvaluator($this->thread);
    }
    
    /**
     * Static method for quick and easy initialization of the Application.
     *
     * If you use this init() method, you cannot specify a service with the
     * name of 'ApplicationConfig' in your service manager config. This name is
     * reserved to hold the array from application.config.php.
     *
     * The following services can only be overridden from application.config.php:
     *
     * - ModuleManager
     * - SharedEventManager
     * - EventManager & Zend\EventManager\EventManagerInterface
     *
     * All other services are configured after module loading, thus can be
     * overridden by modules.
     *
     * @param array $configuration
     * @return Application
     */
    public static function init($configuration = array())
    {
        $smConfig       = isset($configuration['service_manager']) ? $configuration['service_manager'] : array();
        $listeners      = isset($configuration['listeners']) ? $configuration['listeners'] : array();
        $serviceManager = new ServiceManager(new ServiceManagerConfig($smConfig));
        $serviceManager->setService('ApplicationConfig', $configuration);
        
        // By calling engine and flow handler we make all services available
        
        $application = $serviceManager->get('Application')->addListeners($listeners);
        //$serviceManager->get('Persistance');
        
        // Then we can easily attach all listeners without fearing circular
        // dependencies
        
        $serviceManager->get('ListenersAttacher')->attach();
        return $application;
    }
    
	/**
	 * 
	 * @param unknown_type $listeners
	 */
	public function addListeners($listeners = array())
	{
	    $this->defaultListeners = array_unique(array_merge($this->defaultListeners, $listeners));
	    return $this;
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
     * @throws Exception
     * @return \Gbili\Miner\Application\Thread
     */
    public function getThread()
    {
        return $this->thread;
    }
    
    /**
     * 
     * @param Thread $t
     * @throws Exception
     */
    public function run()
    {
        do {
            $this->executeAction();
            $status = $this->flowEvaluator->evaluate();
        } while ($status === FlowEvaluator::EXECUTE 
            || ($status === FlowEvaluator::FAIL && ($status = $this->manageFail()) && $status === FlowEvaluator::EXECUTE)
        );

        return $status;
    }
    
    /**
     * .pre 
     * .success monitor how many actions succeed
     * .fail monitor all actions: normal and optional that fail.
     *     If you want to monitor the number of normal
     *     actions that fail (not optional) and controll whether
     *     the the application should attempt to recover and 
     *     continue, listen to manageFail.normalAction
     *     
     * @throws Exception
     * @return multitype:boolean|\Gbili\Miner\Blueprint\Action\Application\Flow\PlaceNextInterface
     */
    protected function executeAction()
    {
        $this->triggerEvent(    __FUNCTION__ . '.pre');

        if ($this->getThread()->getAction()->execute()) {
            $this->triggerEvent(__FUNCTION__ . '.success');
            $this->manageExecutedAction();
        } else {
            $this->triggerEvent(__FUNCTION__ . '.fail');
        }
        $this->triggerEvent(    __FUNCTION__ . '.post');
    }
    
    /**
     * Called after each execution
     */
    protected function manageExecutedAction()
    {
        $action = $this->getThread()->getAction();
        if ($action->hasFinalResults()) {
            $params = array('results' => $action->spit());
            $this->triggerEvent(__FUNCTION__ . '.hasFinalResults', $params);
        }
    }
    
    /**
     * For a listener to stop execution cycle,
     * it should stop propagation (using a 
     * StopPropagationGauge is an idea
     * 
     * Should either return true if an action
     * has been found that can execute, or false
     * if execution needs to end, or throw...
     * 
     * @throws Exception
     * @return true continue wile loop
     */
    public function manageFail()
    {
        $responses = $this->triggerEvent(__FUNCTION__ . '.normalAction');
        if ($responses->stopped()) {
            return self::NO_MORE_FAILS_ALLOWED;
        }
        return $this->flowEvaluator->attemptFailRecovery();
    }
    
    /**
     * 
     * @param string $event
     * @param array $params
     */
    protected function triggerEvent($event, $params=array())
    {
        return $this->getEventManager()->trigger($event, $this, $this->getParams($params));
    }
    
    /**
     * 
     * @param array $append
     */
    protected function getParams(array $append = array())
    {
        $thread   = $this->getThread();
        $action   = $thread->getAction();
        $actionId = $action->getId();
        $params = compact('thread', 'action', 'actionId');
        
        return (!empty($append))? array_merge($params, $append) : $params;
    }
}
