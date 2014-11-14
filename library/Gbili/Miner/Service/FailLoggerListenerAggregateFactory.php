<?php
namespace Gbili\Miner\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Gbili\Miner\Fail\FailLoggerListenerAggregate;

class FailLoggerListenerAggregateFactory implements FactoryInterface
{
    /**
     * 
     * @param ServiceLocatorInterface $sm
     * @throws Exception
     */
    public function createService(ServiceLocatorInterface $sm)
    {
        // You must set an entry in the service manager such
        // that it can get an instance of your lexer class
        // either by using factories, invokables, directly
        // setting the service etc.
	    return new FailLoggerListenerAggregate($sm->get('FailLogger'));
    }
}