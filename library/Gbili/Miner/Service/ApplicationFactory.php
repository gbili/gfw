<?php
namespace Gbili\Miner\Service;

class ApplicationFactory implements \Zend\ServiceManager\FactoryInterface
{
    /**
     * (non-PHPdoc)
     * @see Zend\ServiceLocatorInterface.FactoryInterface::createService()
     */
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $sm)
    {
		return new \Gbili\Miner\Application\Application($sm->get('FlowEvaluator'));
    }
}
