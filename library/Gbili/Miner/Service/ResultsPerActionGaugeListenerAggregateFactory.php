<?php
namespace Gbili\Miner\Service;

use Gbili\Stdlib\Gauge\GaugeListenerAggregate;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ResultsPerActionGaugeListenerAggregateFactory implements FactoryInterface
{
    /**
     * (non-PHPdoc)
     * @see Zend\ServiceLocatorInterface.FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $sm)
    {
        $gauge = $sm->get('ResultsPerActionGauge');
		$listenerAggregate = new GaugeListenerAggregate($gauge);
		$listenerAggregate->addAttachement('Gbili\Miner\Application\Thread', 'placeSameAction.action' . $gauge->getMonitoredActionId(), 'add', 1000);
		return $listenerAggregate;
    }
}
