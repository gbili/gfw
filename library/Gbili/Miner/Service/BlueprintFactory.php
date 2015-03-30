<?php
namespace Gbili\Miner\Service;

class BlueprintFactory implements \Zend\ServiceManager\FactoryInterface
{
    
    /**
     * 
     * @param \Zend\ServiceManager\ServiceLocatorInterface $sm
     * @throws Exception
     */
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $sm)
    { 
        $config = $sm->get('ApplicationConfig');
        
        if (!isset($config['host'])) {
            throw new Exception('Config must have a \'host\' key set with the hostname to be dumped');
        }
        $host = $config['host'];
        
        if (is_string($host)) {
            $host = new \Gbili\Url\Authority\Host($host);
        }

        if (!$host instanceof \Gbili\Url\Authority\Host) {
            throw new Exception('First param must be a valid url string or a Host instance.');
        }

        $dbReq = \Gbili\Db\Registry::getInstance('\\Gbili\\Miner\\Blueprint');
        $blueprint = new \Gbili\Miner\Blueprint($host, $sm, $dbReq);
        $blueprint->init();
        return $blueprint;
    }
}
