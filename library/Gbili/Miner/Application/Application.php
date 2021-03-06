<?php
namespace Gbili\Miner\Application;

/**
 * 
 * @author g
 *
 */
class Application 
implements \Zend\EventManager\EventManagerAwareInterface,
           \Gbili\Miner\HasAttachableListenersInterface
{
    use \Zend\EventManager\EventManagerAwareTrait;
    
    /**
     * 
     * @var mixed
     */
    protected $defaultListeners = array(
        'LexerListenerAggregate',  // Listen for mined data spitting
        'ExecutionAllowedFailsGaugeListenerAggregate', //manageFail.normalAction
        'FailLoggerListenerAggregate', //executeAction.fail
    );

    //TODO add a method that should handle gauge reachedLimit 
    
    /**
     * 
     * @var FlowEvaluator
     */
    protected $flowEvaluator;
    
    /**
     * 
     * @param FlowEvaluator $thread
     * @param array $thread
     */
    public function __construct(FlowEvaluator $flowEvaluator)
    {
        $this->flowEvaluator = $flowEvaluator;
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
        $appListeners      = isset($configuration['application']['listeners']) ? $configuration['application']['listeners'] : array();
        $serviceManager = new \Zend\ServiceManager\ServiceManager(new \Gbili\Miner\Service\ServiceManagerConfig($smConfig));
        $serviceManager->setService('ApplicationConfig', $configuration);
        
        // By calling engine and flow handler we make all services available

        $application = $serviceManager->get('Application');
        $application->addListeners($appListeners);
        //$serviceManager->get('Persistance');
        
        // Then we can easily attach all listeners without fearing circular
        // dependencies
        
        //The ListenersAttacher has attachables from a ServiceManagerConfig initializer
        //If any retrieved service implements HasAttachableListenersInterface, the initializer
        //will call ListenersAttacher::regsiterAttachable($myService) 
        //Every regsiteredAttachable will be used to call ListenersAttacher::attachListenersToAttachable($myService) 
        //Then we retrieve all the listeners from every myService, and attach them to the service's event manager
        $serviceManager->get('ListenersAttacher')->attach();
        return $application;
    }

    public function setServiceManager(\Zend\ServiceManager\ServiceManager $serviceManager)
    {
        $this->sm = $serviceManager;
        return $this;
    }

    public function getServiceManager()
    {
        if ($this->sm === null) {
            throw new \Exception('ServiceManager not set');
        }
        return $this->sm;
    }
    
	/**
	 * Listeners can be added to the Application. They will be attached by self::init()
	 * @param array $listeners
	 */
	public function addListeners($listeners = array())
	{
        if (!empty($listeners)) {
            $this->defaultListeners = array_merge($this->defaultListeners, $listeners);
        }
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
     * @param Thread $t
     * @throws Exception
     */
    public function run()
    {
        do {
            $this->executeAction();
        } while (
            ($foundExecutableAction = $this->flowEvaluator->evaluate()) 
            || $this->manageNotFoundExecutableAction()
        );
        $this->triggerEvent('enOfScript');
    }
    
    /**
     * .pre 
     * .success monitor how many actions succeed
     * .fail monitor all actions that fail: normal and optional.
     *     If you want to monitor the number of normal
     *     actions that fail (not optional) and controll whether
     *     the the application should attempt to recover and 
     *     continue, listen to manageNotFoundExecutableAction.normalAction
     * .post monitor all executed actions regardless of fail or success
     *     
     * @throws Exception
     * @return void 
     */
    protected function executeAction()
    {
        $this->triggerEvent(    __FUNCTION__ . '.pre');
        if ($this->flowEvaluator->executeActionInFlow()) {
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
        $action = $this->flowEvaluator->getActionInFlow();
        $this->triggerEvent(__FUNCTION__ . '.executed');
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
    public function manageNotFoundExecutableAction()
    {
        $responses = $this->triggerEvent(__FUNCTION__ . '.normalAction');
        return !$responses->stopped() && $this->flowEvaluator->attemptResume();
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
        $thread   = $this->flowEvaluator->getThread();
        $action   = $this->flowEvaluator->getActionInFlow();
        $actionId = $action->getId();
        $params = compact('thread', 'action', 'actionId');
        
        return (!empty($append))? array_merge($params, $append) : $params;
    }
}
