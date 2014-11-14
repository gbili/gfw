<?php
namespace Gbili\Miner\Service;

use Gbili\Stdlib\Gauge\GaugeListenerAggregate;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UnpersistedInstancesGaugeListenerAggregateFactory implements FactoryInterface
{
    /**
     * (non-PHPdoc)
     * @see Zend\ServiceLocatorInterface.FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $sm)
    {
		$listenerAggregate = new GaugeListenerAggregate($sm->get('UnpersistedInstancesGauge'));
		$listenerAggregate->addAttachement('Gbili\Miner\Persistance', 'persistInstance.success', 'subtract', 1000);
	    $listenerAggregate->addAttachement('Gbili\Miner\Persistance', 'addPersistableInstance.pre', 'add', 1000);
	    $listenerAggregate->addAttachement('Gbili\Miner\Persistance', 'persistInstances.post', 'resetCount', 1000);
	    return $listenerAggregate;
    }
}
