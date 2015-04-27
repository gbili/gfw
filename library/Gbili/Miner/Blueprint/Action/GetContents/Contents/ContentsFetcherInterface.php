<?php
namespace Gbili\Miner\Blueprint\Action\GetContents\Contents;

/**
 * 
 * @author gui
 *
 */
interface ContentsFetcherInterface 
{
    public function fetch(\Gbili\Url\UrlInterface $url);
}
