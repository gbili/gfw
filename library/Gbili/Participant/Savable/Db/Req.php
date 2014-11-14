<?php
namespace Gbili\Participant\Savable\Db;

use Gbili\Db\Req\AbstractReq,
    Gbili\Participant\Savable;

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
	public function save(Savable $p)
	{
		$idOrFalse = $this->existsParticipant($p, true);
		if (false === $idOrFalse) {
			$this->insertUpdateData("INSERT INTO VideoEntity_SharedInfo_Participant (vESharedInfoId, mIEId, mIERoleId) VALUES (?, ?, ?)",
								array($p->getSharedInfo()->getId(),
									  $p->getMIE()->getId(),
									  $p->getRole()->getId()));
			$idOrFalse = $this->getAdapter()->lastInsertId();
		}
		$p->setId($idOrFalse);
	}
	
	/**
	 * 
	 * @param unknown_type $title
	 * @param unknown_type $returnIdIfAvailable
	 * @return boolean | integer
	 */
	public function existsParticipant(Savable $p, $returnIdIfAvailable = false)
	{
		return $this->existsElement("SELECT m.participantId AS id FROM VideoEntity_SharedInfo_Participant AS m WHERE m.vESharedInfoId = ? AND m.mIEId = ? AND m.mIERoleId = ?",
									array($p->getSharedInfo()->getId(),
										  $p->getMIE()->getId(),
										  $p->getRole()->getId()),
									'id',
									(boolean) $returnIdIfAvailable);
	}
	
	public function delete(Savable $p)
	{
		
	}
}