<?php
namespace Gbili\Miner;

interface ContentsFetcherAggregateAwareInterface
{
    public function setFetcherAggregate(\Gbili\Miner\Blueprint\Action\GetContents\Contents\ContentsFetcherAggregateInterface $fetcherAggregate);
}
