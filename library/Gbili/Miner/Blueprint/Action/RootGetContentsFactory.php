<?php
namespace Gbili\Miner\Blueprint\Action;

class RootGetContentsFactory implements \Zend\ServiceManager\FactoryInterface
{
    /**
     * 
     * @param ServiceLocatorInterface $sm
     */
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $sm)
    {
        $action = new GetContents\RootGetContents();
        $action->setFetcherAggregate($sm->get('ContentsFetcherAggregate'));
        return $action;
    }
}
