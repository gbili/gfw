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
        $blueprintType = (isset($config['blueprint']['type']) && $config['blueprint']['type'] === 'array')
            ? 'ArrayBlueprint' 
            : 'DbReqBlueprint';
        $blueprintClass = isset($config['blueprint']['class'])
            ? $config['blueprint']['class'] 
            : '\Gbili\Miner\Blueprint\\' . $blueprintType;
        return $sm->get($blueprintClass);
    }
}
