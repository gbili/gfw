<?php
namespace Gbili\Miner\Blueprint\Action\GetContents\Contents;

class FileGetContents
implements ContentsFetcherInterface
{
    public function fetch(\Gbili\Url\UrlInterface $url)
    {
        $result = file_get_contents($url->toString());
        if (false !== $result) {
    	    $result = \Gbili\Encoding\Encoding::utf8Encode($result);
        }
	    return $result;
    }
}

