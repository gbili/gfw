<?php
namespace Gbili\Participant\Role\Savable\Db;

use Gbili\Db\Req\AbstractReq,
    Gbili\Db\Req\Exception,
    Gbili\Participant\Role\Savable;

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
	public function save(Savable $role)
	{
		$idOrFalse = $this->existsRole($role, true);
		if (false === $idOrFalse) {
			if (!$role->getSlug()->isValid()) {
				throw new Exception('The slug is not valid cannot insert it given : ' . print_r($role->getSlug(), true));
			}
			$this->insertUpdateData("INSERT INTO MIERole (name, slug) VALUES (?, ?)",
								array($role->getName(),
									  $role->getSlug()->getValue()));
			$idOrFalse = $this->getAdapter()->lastInsertId();
		}
		$role->setId($idOrFalse);//is now id
	}
	
	/**
	 * 
	 * @param unknown_type $title
	 * @param unknown_type $returnIdIfAvailable
	 * @return boolean | integer
	 */
	public function existsRole(Savable $role, $returnIdIfAvailable = false)
	{
		if (!$role->getSlug()->isValid()) {
			throw new Exception('The slug is not valid cannot insert it given : ' . print_r($role->getSlug(), true));
		}
		return $this->existsElement("SELECT mr.mIERoleId AS id FROM MIERole AS mr WHERE mr.slug = :slug",
									array(':slug' => $role->getSlug()->getValue()),
									'id',
									(boolean) $returnIdIfAvailable);
	}
	
	public function delete(Savable $role)
	{
		
	}
}