<?php
namespace Gbili\Miner\Service;

class ThreadFactory implements \Zend\ServiceManager\FactoryInterface
{
    
    /**
     * 
     * @param \Zend\ServiceManager\ServiceLocatorInterface $sm
     * @throws Exception
     */
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $sm)
    { 
        $blueprint = $sm->get('Blueprint');
        $rootAction = $blueprint->getRoot();
        $thread = new \Gbili\Miner\Application\Thread($rootAction);
        $config = $sm->get('ApplicationConfig');
        if (isset($config['limited_results_action_id'])) {
            $thread->addListener('ResultsPerActionGaugeListenerAggregate');
        }
        return $thread;
    }
}
