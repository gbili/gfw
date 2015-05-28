<?php
namespace Gbili\Miner\Service;

use Gbili\Miner\Gauge\ResultsPerActionGauge;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class ResultsPerActionGaugeFactory implements FactoryInterface
{
    /**
     * 
     * @param ServiceLocatorInterface $sm
     * @throws Exception
     */
    public function createService(ServiceLocatorInterface $sm)
    { 
        $config = $sm->get('ApplicationConfig');
        
        $actionId   = $config['limited_results_action_id'];
        $maxResults = $config['results_per_action_count'];
        
        // Check If action exists
        $blueprint  = $sm->get('Blueprint');
        if (!$blueprint->hasAction($actionId)) {
            throw new \Exception('Trying to monitor results per action on a non existing action id: ' . $actionId);
        }
        $blueprint->getAction($actionId);
        
        $gauge = new ResultsPerActionGauge($maxResults);
        $gauge->setMonitoredActionId($actionId);
        return $gauge;
    }
}
