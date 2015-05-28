<?php
namespace Gbili\Miner\Service;

class FlowEvaluatorFactory implements \Zend\ServiceManager\FactoryInterface
{
    /**
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $sm
     * @throws Exception
     */
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $sm)
    { 
        return new \Gbili\Miner\Application\FlowEvaluator($sm->get('Thread'));
    }
}
