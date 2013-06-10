<?php
namespace Gbili\MIE\Savable\Db;

use Gbili\Db\Req\AbstractReq,
    Gbili\MIE\Savable;

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
	public function save(Savable $mIE)
	{
		$idOrFalse = $this->existsMIE($mIE, true);
		if (false === $idOrFalse) {
			if (!$mIE->getSlug()->isValid()) {
				throw new Exception('The slug is not valid cannot insert it given : ' . print_r($mIE->getSlug(), true));
			}
			$this->insertUpdateData("INSERT INTO MIE (name, slug) VALUES (?, ?)",
								array($mIE->getName(),
									  $mIE->getSlug()->getValue()));
			$idOrFalse = $this->getAdapter()->lastInsertId();
		}
		$mIE->setId($idOrFalse);//is now id
	}
	
	/**
	 * 
	 * @param unknown_type $title
	 * @param unknown_type $returnIdIfAvailable
	 * @return boolean | integer
	 */
	public function existsMIE(Savable $mIE, $returnIdIfAvailable = false)
	{
		if (!$mIE->getSlug()->isValid()) {
			throw new Exception('The slug is not valid sorry cannot query db');
		}
		return $this->existsElement("SELECT m.mIEId AS id FROM MIE AS m WHERE m.slug = :slug",
									array(':slug' => $mIE->getSlug()->getValue()),
									'id',
									(boolean) $returnIdIfAvailable);
	}
	
	public function delete(Savable $mIE)
	{
		
	}
}