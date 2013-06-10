<?php
namespace Gbili\Miner\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Gbili\Miner\Persistance;

/**
 * 
 * @author g
 *
 */
class PersistanceFactory implements FactoryInterface
{
    /**
     * 
     * @param ServiceLocatorInterface $sm
     * @return \Gbili\Miner\Persistance
     */
    public function createService(ServiceLocatorInterface $sm)
    {
        return new Persistance($sm->get('ApplicationConfig'), $sm);
    }
}