<?php
namespace Gbili\Miner\Service;

use Gbili\Miner\Gauge\UnpersistedInstancesGauge;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class UnpersistedInstancesGaugeFactory implements FactoryInterface
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
	    
        if (!isset($config['unpersisted_instances_max_count'])) {
            throw new Exception('ApplicationConfig must have a \'unpersisted_instances_max_count\' key');
        }
        
        $max = $config['unpersisted_instances_max_count'];
	    return new UnpersistedInstancesGauge($max);
    }
}