<?php
namespace Gbili\Tests\Miner\Blueprint\Action\GetContents\Contents;

class MockUnsuccessfulContentsFetcher implements \Gbili\Miner\Blueprint\Action\GetContents\Contents\ContentsFetcherInterface
{
    public function fetch(\Gbili\Url\UrlInterface $url)
    {
        return false;
    }
}
