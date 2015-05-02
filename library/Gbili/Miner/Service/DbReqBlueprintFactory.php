<?php
namespace Gbili\Miner\Service;

class DbReqBlueprintFactory implements \Zend\ServiceManager\FactoryInterface
{
    
    /**
     * 
     * @param \Zend\ServiceManager\ServiceLocatorInterface $sm
     * @throws Exception
     */
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $sm)
    { 
        $config = $sm->get('ApplicationConfig');
        $blueprint = new \Gbili\Miner\Blueprint\DbReqBlueprint($sm);
        $dbReq = (isset($config['db_req']))
            ? $config['db_req'] 
            : \Gbili\Db\Registry::getInstance('\Gbili\Miner\Blueprint\DbReqBlueprint');
        $blueprint->setDbReq($dbReq);
        $blueprint->init();
        return $blueprint;
    }
}
