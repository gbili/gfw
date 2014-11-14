<?php
namespace Gbili\Miner\Service;

use Gbili\Miner\Application\Application;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ApplicationFactory implements FactoryInterface
{
    /**
     * (non-PHPdoc)
     * @see Zend\ServiceLocatorInterface.FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $sm)
    {
		return new Application($sm->get('Thread'), $sm->get('ApplicationConfig'));
    }
}