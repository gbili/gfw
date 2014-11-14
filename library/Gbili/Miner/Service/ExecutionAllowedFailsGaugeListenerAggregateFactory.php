<?php
namespace Gbili\Miner\Service;

use Gbili\Stdlib\Gauge\GaugeListenerAggregate;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ExecutionAllowedFailsGaugeListenerAggregateFactory implements FactoryInterface
{
    /**
     * (non-PHPdoc)
     * @see Zend\ServiceLocatorInterface.FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $sm)
    {
		$listenerAggregate = new GaugeListenerAggregate($sm->get('ExecutionAllowedFailsGauge'));
		$listenerAggregate->addAttachement('Gbili\Miner\Application\Application', 'manageFail.normalAction', 'subtract', 1000);
	    $listenerAggregate->addAttachement('Gbili\Miner\Application\Application', 'executeAction.success', 'add', 1000);
	    return $listenerAggregate;
    }
}
