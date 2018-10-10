<?php
namespace Gbili\Miner\Blueprint\Action\GetContents\Contents;

class DbReq
implements ContentsFetcherInterface
{
    public function fetch(\Gbili\Url\UrlInterface $url);
    {
        return \Gbili\Db\Registry::getInstance('\Gbili\Miner\Blueprint\Action\GetContents\Contents\Savable')->getContents($url);
    }
}

