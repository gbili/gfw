<?php
namespace Gbili\Tests\Miner\Blueprint\Action\GetContents\Contents;

class MockContentsFetcher2 implements \Gbili\Miner\Blueprint\Action\GetContents\Contents\ContentsFetcherInterface
{
    public function fetch(\Gbili\Url\UrlInterface $url)
    {
        return get_class($this);
    }
}
