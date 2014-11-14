<?php
namespace Gbili\Miner\Service;

use Gbili\Miner\Gauge\ExecutionAllowedFailsGauge;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class ExecutionAllowedFailsGaugeFactory implements FactoryInterface
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
	    
        if (!isset($config['execution_allowed_fails_max_count'])) {
            throw new Exception('ApplicationConfig must have a \'execution_allowed_fails_max_count\' key');
        }
        
	    return new ExecutionAllowedFailsGauge($config['execution_allowed_fails_max_count']);
    }
}