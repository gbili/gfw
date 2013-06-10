<?php
namespace Gbili\Miner\Service;

use Gbili\Url\Authority\Host;
use Gbili\Miner\Blueprint;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class BlueprintFactory implements FactoryInterface
{
    
    /**
     * 
     * @param ServiceLocatorInterface $sm
     * @throws Exception
     */
    public function createService(ServiceLocatorInterface $sm)
    { 
        $config = $sm->get('ApplicationConfig');
        
        if (!isset($config['host'])) {
            throw new Exception('Config must have a \'host\' key set with the hostname to be dumped');
        }
        $host = $config['host'];
        
        if (is_string($host)) {
            $host = new Host($host);
        }
        
        if (!$host instanceof Host) {
            throw new Exception('First param must be a valid url string or a Host instance.');
        }
        return new Blueprint($host, $sm);
    }
}