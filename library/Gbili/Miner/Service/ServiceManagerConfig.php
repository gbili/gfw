<?php
namespace Gbili\Miner\Service;

use Gbili\Miner\AttachableListenersInterface;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class ServiceManagerConfig implements ConfigInterface
{
    /**
     * Services that can be instantiated without factories
     *
     * @var array
     */
    protected $invokables = array(
        'SharedEventManager' => 'Zend\EventManager\SharedEventManager',
    );

    /**
     * Service factories
     *
     * @var array
     */
    protected $factories = array(
        'FailLogger'                                 => 'Gbili\Miner\Service\FailLoggerFactory',
        'FailLoggerListenerAggregate'                => 'Gbili\Miner\Service\FailLoggerListenerAggregateFactory',
        'PersistanceListenerAggregate'               => 'Gbili\Miner\Service\PersistanceListenerAggregateFactory', 
        'LexerListenerAggregate'                     => 'Gbili\Miner\Service\LexerListenerAggregateFactory', 
        'Thread'                                     => 'Gbili\Miner\Service\ThreadFactory',
        'Application'                                => 'Gbili\Miner\Service\ApplicationFactory',
        'ApplicationListenerAggregate'               => 'Gbili\Miner\Service\ApplicationListenerAggregateFactory',
        'Blueprint'                                  => 'Gbili\Miner\Service\BlueprintFactory',
        'PersistanceAllowedFailsGauge'                  => 'Gbili\Miner\Service\PersistanceAllowedFailsGaugeFactory',
        'PersistanceAllowedFailsGaugeListenerAggregate' => 'Gbili\Miner\Service\PersistanceAllowedFailsGaugeListenerAggregateFactory',
        'ExecutionAllowedFailsGauge'                    => 'Gbili\Miner\Service\ExecutionAllowedFailsGaugeFactory',
        'ExecutionAllowedFailsGaugeListenerAggregate'   => 'Gbili\Miner\Service\ExecutionAllowedFailsGaugeListenerAggregateFactory',
        'UnpersistedInstancesGauge'                  => 'Gbili\Miner\Service\UnpersistedInstancesGaugeFactory',
        'UnpersistedInstancesGaugeListenerAggregate' => 'Gbili\Miner\Service\UnpersistedInstancesGaugeListenerAggregateFactory', 
        'ResultsPerActionGauge'                      => 'Gbili\Miner\Service\ResultsPerActionGaugeFactory',
        'ResultsPerActionGaugeListenerAggregate'     => 'Gbili\Miner\Service\ResultsPerActionGaugeListenerAggregateFactory',
        'Delay'                                      => 'Gbili\Miner\Service\DelayFactory',
        'ListenersAttacher'                          => 'Gbili\Miner\Service\ListenersAttacherFactory',
        'Persistance'                                => 'Gbili\Miner\Service\PersistanceFactory',
    );

    /**
     * Abstract factories
     *
     * @var array
     */
    protected $abstractFactories = array();

    /**
     * Aliases
     *
     * @var array
     */
    protected $aliases = array(
        'Zend\EventManager\EventManagerInterface' => 'EventManager',
    );

    /**
     * Shared services
     *
     * Services are shared by default; this is primarily to indicate services
     * that should NOT be shared
     *
     * @var array
     */
    protected $shared = array(
        //'EventManager' => false,
        'PersistableInstance' => false,
    );

    /**
     * Constructor
     *
     * Merges internal arrays with those passed via configuration
     *
     * @param  array $configuration
     */
    public function __construct(array $configuration = array())
    {
        if (isset($configuration['invokables'])) {
            $this->invokables = array_merge($this->invokables, $configuration['invokables']);
        }

        if (isset($configuration['factories'])) {
            $this->factories = array_merge($this->factories, $configuration['factories']);
        }

        if (isset($configuration['abstract_factories'])) {
            $this->abstractFactories = array_merge($this->abstractFactories, $configuration['abstract_factories']);
        }

        if (isset($configuration['aliases'])) {
            $this->aliases = array_merge($this->aliases, $configuration['aliases']);
        }

        if (isset($configuration['shared'])) {
            $this->shared = array_merge($this->shared, $configuration['shared']);
        }
    }

    /**
     * Configure the provided service manager instance with the configuration
     * in this class.
     *
     * In addition to using each of the internal properties to configure the
     * service manager, also adds an initializer to inject ServiceManagerAware
     * and ServiceLocatorAware classes with the service manager.
     *
     * @param  ServiceManager $serviceManager
     * @return void
     */
    public function configureServiceManager(ServiceManager $serviceManager)
    {
        foreach ($this->invokables as $name => $class) {
            $serviceManager->setInvokableClass($name, $class);
        }

        foreach ($this->factories as $name => $factoryClass) {
            $serviceManager->setFactory($name, $factoryClass);
        }

        foreach ($this->abstractFactories as $factoryClass) {
            $serviceManager->addAbstractFactory($factoryClass);
        }

        foreach ($this->aliases as $name => $service) {
            $serviceManager->setAlias($name, $service);
        }

        foreach ($this->shared as $name => $value) {
            $serviceManager->setShared($name, $value);
        }

        /*$serviceManager->addInitializer(function ($instance) use ($serviceManager) {
            if ($instance instanceof EventManagerAwareInterface) {
                if ($instance->getEventManager() instanceof EventManagerInterface) {
                    $instance->getEventManager()->setSharedManager(
                        $serviceManager->get('SharedEventManager')
                    );
                } else {
                    $instance->setEventManager($serviceManager->get('EventManager'));
                }
            }
        });*/

        $serviceManager->addInitializer(function ($instance) use ($serviceManager) {
            if ($instance instanceof ServiceManagerAwareInterface) {
                $instance->setServiceManager($serviceManager);
            }
        });
        
        $serviceManager->addInitializer(function ($instance) use ($serviceManager) {
            if ($instance instanceof ServiceLocatorAwareInterface) {
                $instance->setServiceLocator($serviceManager);
            }
        });
        
        $serviceManager->addInitializer(function ($instance) use ($serviceManager) {
            if ($instance instanceof AttachableListenersInterface) {
                $serviceManager->get('ListenersAttacher')->registerAttachable($instance);
            }
        });
        
        $serviceManager->setService('ServiceManager', $serviceManager);
    }
}