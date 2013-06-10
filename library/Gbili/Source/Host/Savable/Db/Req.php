<?php
namespace Gbili\Source\Host\Savable\Db;

use Gbili\Source\Host\Savable as SavableHost;
use Gbili\Db\Req\AbstractReq;
use Gbili\Db\Req\Exception;

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
	public function save(SavableHost $h)
	{
		if (!$this->hasTableName()) {
			throw new Exception('the table name must be set');
		}
		$idOrFalse = $this->existsSourceHost($h, true);
		//only insert if does not already exist
		if (false === $idOrFalse) {
			$sql = "INSERT INTO {$this->getTableName()} (host, hFName) VALUES (:host, :hFName)";
			$this->insertUpdateData($sql, array(
			    ':host' => $h->getHost()->toString(),
				':hFName'  => ($h->hasUserFriendlyName())? $h->getUserFriendlyName() : ''
			));
			$idOrFalse = $this->getAdapter()->lastInsertId();
		}
		//$idOrFalse is an id
		$h->setId($idOrFalse);
	}
	
	/**
	 * 
	 * @param unknown_type $title
	 * @param unknown_type $returnIdIfAvailable
	 * @return boolean | integer
	 */
	public function existsSourceHost(SavableHost $host, $returnIdIfAvailable = false)
	{
		return $this->existsElement("SELECT t.hostId AS id FROM {$this->getTableName()} AS t WHERE t.host = :host",
									array(':host' => (string) $host->getHost()->toString()),
									'id',
									(boolean) $returnIdIfAvailable);
	}
	
	public function delete()
	{
		
	}
}