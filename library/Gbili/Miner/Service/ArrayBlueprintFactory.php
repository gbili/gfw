<?php
namespace Gbili\Miner\Service;

class ArrayBlueprintFactory implements \Zend\ServiceManager\FactoryInterface
{
    
    /**
     * 
     * @param \Zend\ServiceManager\ServiceLocatorInterface $sm
     * @throws Exception
     */
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $sm)
    { 
        $blueprint = new \Gbili\Miner\Blueprint\ArrayBlueprint($sm);
        $blueprint->init();
        return $blueprint;
    }
}
