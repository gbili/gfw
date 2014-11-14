<?php
namespace Gbili\Video\Savable\Db;

use Gbili\Db\Req\AbstractReq,
    Gbili\MIE\Savable as MIESavable;

class Req
extends AbstractReq
{
	/**
	 * 
	 * @return unknown_type
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function save(MIESavable $mIE)
	{
		$sql = "INSERT INTO MIE (name, slug)";
	}
	
	public function delete(MIESavable $mIE)
	{
		
	}
}