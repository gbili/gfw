<?php
namespace Gbili\Miner\Blueprint\Action;

use Gbili\Stdlib\Delay;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GetContentsFactory implements FactoryInterface
{
    /**
     * 
     * @param ServiceLocatorInterface $sm
     */
    public function createService(ServiceLocatorInterface $sm)
    {
        $action = new GetContents();
        $action->setFetcherAggregate($sm->get('ContentsFetcherAggregate'));
        return $action;
    }
}
