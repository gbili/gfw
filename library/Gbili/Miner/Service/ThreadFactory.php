<?php
namespace Gbili\Miner\Service;

use Gbili\Miner\Application\Thread;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class ThreadFactory implements FactoryInterface
{
    
    /**
     * 
     * @param ServiceLocatorInterface $sm
     * @throws Exception
     */
    public function createService(ServiceLocatorInterface $sm)
    { 
        return new Thread($sm->get('Blueprint'));
    }
}
