<?php
namespace Gbili\Miner\Service;

use Gbili\Miner\Gauge\PersistanceAllowedFailsGauge;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class PersistanceAllowedFailsGaugeFactory implements FactoryInterface
{
    /**
     * 
     * @param ServiceLocatorInterface $sm
     * @throws Exception
     */
    public function createService(ServiceLocatorInterface $sm)
    { 
        $config = $sm->get('ApplicationConfig');
	    $engine = $sm->get('Persistance');
	    
        if (!isset($config['persistance_allowed_fails_max_count'])) {
            throw new Exception('ApplicationConfig must have a \'persistance_allowed_fails_max_count\' key');
        }
        
	    $p = new PersistanceAllowedFailsGauge($config['persistance_allowed_fails_max_count']);
	    echo 'got an object';
	    return $p;
    }
}