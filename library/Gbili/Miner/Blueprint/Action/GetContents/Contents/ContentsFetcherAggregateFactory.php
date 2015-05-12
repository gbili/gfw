<?php
namespace Gbili\Miner\Blueprint\Action\GetContents\Contents;

class ContentsFetcherAggregateFactory implements \Zend\ServiceManager\FactoryInterface
{
    /**
     * 
     * @param \Zend\ServiceManager\ServiceLocatorInterface $sm
     * @throws Exception
     */
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $sm)
    { 
        $fetcherAggregate = new ContentsFetcherAggregate();

        //Default fetchers

        $config = $sm->get('ApplicationConfig');

        if (!isset($config['contents_fetcher_aggregate']['queue'])) {
            $config['contents_fetcher_aggregate'] = [ 
                'queue' => [
                    1 => [new Savable,],
                    9 => [new FileGetContents,],
                ],
            ];
        }

        foreach ($config['contents_fetcher_aggregate']['queue'] as $priority => $listOfFetchersInSamePriority) {
            if (!is_array($listOfFetchersInSamePriority)) {
                $listOfFetchersInSamePriority = array($listOfFetchersInSamePriority);
            }
            foreach ($listOfFetchersInSamePriority as $fetcher) {
                $fetcherAggregate->addFetcher($fetcher, $priority);
            }
        }

        return $fetcherAggregate;
    }
}
