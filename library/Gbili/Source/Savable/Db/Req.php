<?php
namespace Gbili\Source\Savable\Db;

use Gbili\Db\Req\AbstractReq,
    Gbili\Db\Req\Exception,
    Gbili\Source\Savable;
/**
 * 
 * @author gui
 *
 */
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
	 * @return void
	 */
	public function save(Savable $s)
	{
		if (!$this->hasTableName()) {
			throw new Exception('the table name must be set');
		}
		$idOrFalse = $this->existsSource($s, true);
		//only insert if does not already exist
		if (false === $idOrFalse) {
			$sql = "INSERT INTO {$this->getTableName()} (hostId, path) VALUES (:hostId, :path)";
			$this->insertUpdateData($sql, array(':hostId' => $s->getHost()->getId(),
												':path'  => (string) $s->getPath()));
			$idOrFalse = $this->getAdapter()->lastInsertId();
		}
		//$idOrFalse is an id
		$s->setId($idOrFalse);
	}
	
	/**
	 * 
	 * @param unknown_type $title
	 * @param unknown_type $returnIdIfAvailable
	 * @return boolean | integer
	 */
	public function existsSource(Savable $source, $returnIdIfAvailable = false)
	{
		return $this->existsElement("SELECT t.sourceId AS id FROM {$this->getTableName()} AS t WHERE t.path = :path AND t.hostId = :hostId",
									array(':path' => (string) $source->getPath(),
										  ':hostId' => $source->getHost()->getId()),
									'id',
									(boolean) $returnIdIfAvailable);
	}
	
	public function delete()
	{
		
	}
}