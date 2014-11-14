<?php
namespace Gbili\Miner;


use Zend\ServiceManager\ServiceManager;
use Zend\EventManager\Event;

use Gbili\Miner\Persistance\Gauge\PersistanceAllowedFailsGauge;
use Gbili\Miner\Persistance\Gauge\UnpersistedInstancesGauge;

use Zend\EventManager\EventManagerAwareTrait;
use Zend\EventManager\EventManagerAwareInterface;

use Gbili\Miner\Persistance\PersistableInterface;
use Gbili\Db\Registry          as DbRegistry;


/**
 * 
 * @author gui
 *
 */
class Persistance implements EventManagerAwareInterface, AttachableListenersInterface
{
    use EventManagerAwareTrait;
    
    protected $defaultListeners = array(
        'LexerListenerAggregate',
        'PersistanceAllowedFailsGaugeListenerAggregate',
        'UnpersistedInstancesGaugeListenerAggregate',
        'FailLoggerListenerAggregate', // persistIntstance.fail
    );
    
    /**
     * 
     * @var mixed
     */
    protected $configuration = null;
    
    /**
     * 
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager = null;
    
	/**
	 * Contains the class instances that are populated with the results
	 * by the lexer
	 * 
	 * @var multitype
	 */
	 protected $persistableInstances = array();
	
	/**
	 * 
	 * @param unknown_type $configuration
	 * @param ServiceManager $serviceManager
	 */
	public function __construct($configuration, ServiceManager $serviceManager)
	{
	    $this->configuration  = $configuration;
	    $this->serviceManager = $serviceManager;
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
	 */
	public function getServiceManager()
	{
	    return  $this->serviceManager;
	}
	
	/**
	 * If we have already added an persistable instance, it means
	 * this is at least the second time that we call this method.
	 * In that case, we want to check if something has to be done
	 * before a new one is efectevely added : addPersistableInstance.pre
	 * For example: 
     *      save the previously added persistable instances.. 
	 * 
	 * Saving this one would make little sense since no data would
	 * be available to the instance. So we would save nothing
	 */
	public function addPersistableInstance()
	{
	    if (!empty($this->persistableInstances)) {
	        $this->getEventManager()->trigger(__FUNCTION__ . '.pre', $this);
	    }
	    $persistableInstance = $this->getServiceManager()->get('PersistableInstance');
	    $this->persistableInstances[] = $persistableInstance;
        $this->getEventManager()->trigger(__FUNCTION__ . '.post', $this, compact('persistableInstance'));
	}
	
	/**
	 * Save the instances generated during dumping process to the database
	 */
	public function persistInstances()
	{
		foreach ($this->persistableInstances as $instance) {
            $this->persistInstance($instance);
		}
	    $this->persistableInstances = array();
	    $this->getEventManager()->trigger(__FUNCTION__ . '.post', $this);
	}
	
	/**
	 * 
	 * @param ActiveRecordInterface $persistableInstance
	 */
	private function persistInstance(PersistableInterface $persistableInstance)
	{
	    try {
	        $persistableInstance->persist();
	        $params = compact('persistableInstance');
	        $this->getEventManager()->trigger(__FUNCTION__ . '.success', $this, $params);
	    } catch (Exception $exception) {
	        $params = compact('exception', 'persistableInstance');
	        $responses = $this->getEventManager()->trigger(__FUNCTION__ . '.fail'   , $this, $params);
	        if ($responses->stopped()) {
	            throw $exception;
	        }
	    }
	}
	
	/**
	 * Called by gauge if too many instances fail
	 */
	public function terminate(Event $e)
	{
	    $params = $e->getParams();
	    //Out::l1('Number of allowed fails with no success, exceeded. Number of subsequent fails allowed : ' . $this->getPersistanceAllowedFailsGauge()->getMax() . ". Remaining fails allowed with no success : {$this->getPersistanceAllowedFailsGauge()->getCount()}");
	    $this->saveNewInstanceGeneratingPointData();
	    exit($e->getMessage());//the script
	}
}
