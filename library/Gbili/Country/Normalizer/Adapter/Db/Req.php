<?php
namespace Gbili\Country\Normalizer\Adapter\Db;

use Gbili\Db\Req\AbstractReq,
    Gbili\Country\Normalizer\Adapter\Db\DbInterface,
    Gbili\Country\Normalizer\Adapter\Storage,
    PDO;

class Req
extends AbstractReq
implements DbInterface
{
	/**
	 * 
	 * @return unknown_type
	 */
	public function __construct()
	{
		parent::__construct();
		$this->baseSql = "SELECT c.name AS countryName,
					  cm.countryId AS countryId,
					  cm.regex AS regex,
					  GROUP_CONCAT(l.value) AS langISO
			   	FROM Country_Matcher AS cm 
			   		INNER JOIN Country AS c 
			   			ON (cm.countryId = c.countryId)
			   		INNER JOIN Country_r_LangISOs AS cl 
			   			ON (cm.countryId = cl.countryId)
			   		INNER JOIN LangISO AS l
			   			ON (l.langISOId = cl.langISOId)
			   	GROUP BY c.name 
			   	ORDER BY cm.priority 
			   	ASC";
	}
	
	/**
	 * return the next block of records
	 * depending on fetch count
	 * 
	 * @param $fetchCount
	 * @return unknown_type
	 */
	public function getBlock($fetchCount)
	{
		$start = $fetchCount * Storage::$blockMaxSize;
		$sql = $this->baseSql . " LIMIT $start, " . Storage::$blockMaxSize;
		return $this->getResultSet($sql);
	}
	
	/**
	 * Get all records at once
	 * 
	 * @return unknown_type
	 */
	public function getAll()
	{
		return $res = $this->getResultSet($this->baseSql);
	}
	
	/**
	 * Get one record
	 * 
	 * @param unknown_type $fetchCount
	 * @return unknown_type
	 */
	public function getOne($fetchCount)
	{
		$sql = $this->baseSql . " LIMIT $fetchCount, 1";
		return $this->getResultSet($sql);
	}

	/**
	 * (non-PHPdoc)
	 * @see International/Country/Normalizer/Adapter/Db/Storage_Interface#getCountries()
	 */
	public function getCountries()
	{
		return $this->getResultSet("SELECT c.name AS countryName
										FROM Country_Matcher AS cm
											INNER JOIN Country AS c ON (cm.countryId = c.countryId)
										ORDER BY c.name",
									array(),
									PDO::FETCH_NUM);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see International/Country/Normalizer/Adapter/Db/Storage_Interface#isSupportedCountry()
	 */
	public function isSupportedCountry($str)
	{
		return (boolean) $this->getResultSet("SELECT c.name AS countryName
												FROM Country_Matcher AS cm
													INNER JOIN Country AS c ON (cm.countryId = c.countryId)
												WHERE c.name = :countryName", 
											array(':countryName' => $str));
	}
	
	/**
	 * 
	 * @param unknown_type $dirtyCountryStr
	 * @return unknown_type
	 */
	public function saveDirtyCountryStr($dirtyCountryStr)
	{
		if (false === $idOrFalse = $this->existsDirtyCountryStr($dirtyCountryStr)) {
			$this->insertUpdateData("INSERT INTO DirtyCountry (name) VALUES (:name)", array(':name' => $dirtyCountryStr));
			$idOrFalse = $this->getAdapter()->lastInsertId();
		}
		return $idOrFalse;
	}
	
	/**
	 * 
	 * @param $dirtyCountryStr
	 * @return unknown_type
	 */
	public function existsDirtyCountryStr($dirtyCountryStr)
	{
		return $this->existsElement("SELECT d.dirtyCountryId AS id FROM DirtyCountry AS d WHERE d.name = :name",
									array(':name' => $dirtyCountryStr),
									'id',
									true);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see International/Country/Normalizer/Adapter/Db/Storage_Interface#isSupportedCountry()
	 */
	public function getCountryLangISO($str)
	{
		$this->getResultSet("SELECT GROUP_CONCAT(l.value) AS langISO
								FROM LangISO AS l
									INNER JOIN Country_r_LangISOs AS cl ON (l.langISOId = cl.langISOId)
									INNER JOIN Country AS c ON (cl.countryId = c.countryId)
								WHERE c.name = :countryName
								GROUP BY c.name",
							array(':countryName' => $str));
	}
}