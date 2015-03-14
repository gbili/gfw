<?php
namespace Gbili\Miner\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Gbili\Miner\ListenersAttacher;

/**
 * 
 * @author g
 *
 */
class ListenersAttacherFactory implements FactoryInterface
{
    /**
     * 
     * @param ServiceLocatorInterface $sm
     * @return \Gbili\Miner\Persistance
     */
    public function createService(ServiceLocatorInterface $sm)
    {
        return new ListenersAttacher($sm);
    }
}
