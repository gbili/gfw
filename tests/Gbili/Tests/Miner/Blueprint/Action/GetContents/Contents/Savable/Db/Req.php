<?php
namespace Gbili\Tests\Miner\Blueprint\Action\GetContents\Contents\Savable\Db;

use Gbili\Miner\Blueprint\Action\GetContents\Contents\Savable as ContentsSavable;
use Gbili\Url\Url;

class Req
extends \Gbili\Db\Req\AbstractReq
{
	/**
	 * This will get the paths an new instance
	 * generating point action id
	 * 
	 * @param Host $host
	 * @return unknown_type
	 */
	public function getContents(Url $url)
	{
	    return 'SomeContents';
	}
	
	/**
	 * 
	 * @param Url $url
	 * @param unknown_type $contents
	 */
	protected function insertContent(Url $url, $contents)
	{
        return true;
	}
	
	/**
	 * 
	 * @param Url $url
	 * @param unknown_type $returnId
	 */
	protected function existsContent(Url $url, $returnId = false)
	{
        return true;
	}
	
	/**
	 * 
	 * @param ContentsSavable $c
	 * @return boolean
	 */
	public function save(ContentsSavable $c)
	{
        $c->setId(1);
	}
}
