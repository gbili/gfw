<?php
/**
 * The difference between savable\db\req (this) and db\req, is that the savable
 * saves the data needed for scraping, whereas db\req retrieves it?
 * One is used by the user to save his actions data, and the latter is used
 * by the engine to start scraping
 * 
 * @author g
 *
 */
namespace Gbili\Miner\Blueprint\Savable\Db;

use Gbili\Db\Req\AbstractReq;
use Gbili\Db\Registry;
use Gbili\Db\Req\Exception;
use Gbili\Url\Authority\Host;
use Gbili\Miner\Blueprint\Savable   as BlueprintSavable;

class Req extends AbstractReq
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
	 * @param Savable $blueprint
	 * @return unknown_type
	 */
	public function save(BlueprintSavable $b)
	{
		//don't save if it already exists
		$res = $this->existsBlueprint($b->getHost()->toString(), true);
		//if it doesn't exist make insert
		if (false === $res) {
			$sql = 'INSERT INTO Blueprint
						(host)
						VALUES (:host)';
			$this->insertUpdateData($sql, 
									array(':host' => $b->getHost()->toString()));
			$res = $this->getAdapter()->lastInsertId();
		}
		//set the blueprint id
		$b->setId($res);
	}
	
	/**
	 * 
	 * @return bool | integer
	 */
	public function existsBlueprint($hostOrId, $returnId = false)
	{
		$column = (is_numeric($hostOrId))? 'bId' : 'host';
		$sql = "SELECT b.bId as bId
					FROM Blueprint AS b
					WHERE b.$column = :column";
		return $this->existsElement($sql,
									array(':column' => $hostOrId),
									(($returnId)? 'bId' : null));
	}
	
	/**
	 * 
	 * @param $blueprintId
	 * @param $actionId
	 * @return unknown_type
	 */
	public function updateBlueprintNewInstanceGeneratingPointActionId($blueprintId, $actionId)
	{
		if (!Registry::getInstance('\Gbili\Miner\Blueprint\Savable')->existsBlueprint((integer) $blueprintId)) {
			throw new Exception('You are trying to update an unexisting blueprint\'s newInstanceGeneratingPointActionId given bId : ' . print_r($blueprintId, true));
		}
		$sql = "UPDATE Blueprint
					SET newInstanceGeneratingPointActionId = :actionId
					WHERE bId = :bId";
		return $this->insertUpdateData($sql, array(':actionId' => (integer) $actionId,
												   ':bId'	   => (integer) $blueprintId));
	}
}
