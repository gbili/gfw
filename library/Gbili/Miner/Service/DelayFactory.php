<?php
namespace Gbili\Miner\Service;

use Gbili\Stdlib\Delay;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DelayFactory implements FactoryInterface
{
    /**
     * 
     * @param ServiceLocatorInterface $sm
     */
    public function createService(ServiceLocatorInterface $sm)
    {
        $config = $sm->get('ApplicationConfig');
        $min = (isset($config['delay_min']))? $config['delay_min'] : 20;
        $max = (isset($config['delay_max']))? $config['delay_max'] : 25;
        return new Delay($min, $max);
    }
}
