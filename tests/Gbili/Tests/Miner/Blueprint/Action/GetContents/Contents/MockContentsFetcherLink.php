<?php
namespace Gbili\Tests\Miner\Blueprint\Action\GetContents\Contents;

class MockContentsFetcherLink implements \Gbili\Miner\Blueprint\Action\GetContents\Contents\ContentsFetcherInterface
{
    public function fetch(\Gbili\Url\UrlInterface $url)
    {
        if (false === strpos($url->toString(), 'http://link')) {
            return false;
        }
        return 'This is the content you get when you follow the link';
    }
}
