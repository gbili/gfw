<?php
namespace Gbili\Miner\Blueprint\Action;

use Gbili\Stdlib\Delay;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ExtractFactory implements FactoryInterface
{
    /**
     * 
     * @param ServiceLocatorInterface $sm
     */
    public function createService(ServiceLocatorInterface $sm)
    {
        return new Extract();
    }
}
