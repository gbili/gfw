<?php
namespace Gbili\Miner\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Gbili\Miner\Application\ApplicationListenerAggregate;

class ApplicationListenerAggregateFactory implements FactoryInterface
{
    /**
     * 
     * @param ServiceLocatorInterface $sm
     * @throws Exception
     */
    public function createService(ServiceLocatorInterface $sm)
    {
	    return new ApplicationListenerAggregate($sm->get('Application'));
    }
}