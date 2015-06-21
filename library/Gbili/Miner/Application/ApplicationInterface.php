<?php
namespace Gbili\Miner\Application;

/**
 * 
 * @author g
 *
 */
interface ApplicationInterface 
{
    /**
     * @param FlowEvaluator $flowEvaluator
     */
    public function __construct(FlowEvaluator $flowEvaluator);

    /**
     * @return FlowEvaluator
     */
    public function getFlowEvaluator();

    /**
     * @param FlowEvaluator $flowEvaluator
     * @return Application
     */
    public function setFlowEvaluator(FlowEvaluator $flowEvaluator);

    /**
     * @param \Zend\ServiceManager\ServiceManagerInterface $serviceManager
     * @return self
     */
    public function setServiceManager(\Zend\ServiceManager\ServiceManager $serviceManager);

    /**
     * @return \Zend\ServiceManager\ServiceManagerInterface
     */
    public function getServiceManager();
    
	/**
	 * Listeners can be added to the Application. They will be attached by self::init()
	 * @param array $listeners
     * @return self
	 */
	public function addListeners($listeners = array());
    
    /**
     * List of listeners they will be attached by the
     * ListenersAttacher to the application
     * @return array
     * @return void
     */
    public function getListeners();
    
    /**
     * Run the application:
     *    Execute actions placed into flow
     *    by the flowEvaluator
     * @throws Exception
     * @return void
     */
    public function run();
    
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
    public function manageNotFoundExecutableAction();
}
