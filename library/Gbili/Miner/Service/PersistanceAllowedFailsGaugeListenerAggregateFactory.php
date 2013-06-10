<?php
namespace Gbili\Miner\Service;

use Gbili\Stdlib\Gauge\GaugeListenerAggregate;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PersistanceAllowedFailsGaugeListenerAggregateFactory implements FactoryInterface
{
    /**
     * (non-PHPdoc)
     * @see Zend\ServiceLocatorInterface.FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $sm)
    {
		$listenerAggregate = new GaugeListenerAggregate($sm->get('PersistanceAllowedFailsGauge'));
		$listenerAggregate->addAttachement('Gbili\Miner\Persistance', 'persistInstance.fail', 'subtract', 1000);
	    $listenerAggregate->addAttachement('Gbili\Miner\Persistance', 'persistInstance.success', 'add', 1000);
	    return $listenerAggregate;
    }
}
