<?php
namespace Gbili\Lang\ISO\Normalizer\Adapter\Db;

use Gbili\Db\Req\AbstractReq;
use Gbili\Lang\ISO\Normalizer\Adapter\Db as DbAdapter,
    PDO;

/**
 * 
 * @author gui
 *
 */
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
		//gets each langISO value with its regex
		$this->baseSql = "SELECT l.value AS langISO,
								  l.langISOId AS langISOId,
								  lm.regex AS regex
						   	FROM LangISO_Matcher AS lm 
						   		INNER JOIN LangISO AS l
						   			ON (lm.langISOId = l.langISOId) 
						   	ORDER BY lm.priority 
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
		$start = $fetchCount * DbAdapter::$blockMaxSize;
		$sql = $this->baseSql . " LIMIT $start, " . DbAdapter::$blockMaxSize;
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
	 * @see International/Country/Normalizer/Adapter/Db/Country_Normalizer_Adapter_Db_Interface#getCountries()
	 */
	public function getLangISOs()
	{
		return $this->getResultSet("SELECT l.value AS langISO
										FROM LangISO AS l
										ORDER BY l.value",
									array(),
									PDO::FETCH_NUM);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see International/Country/Normalizer/Adapter/Db/Country_Normalizer_Adapter_Db_Interface#isSupportedCountry()
	 */
	public function isSupportedLangISO($str)
	{
		return $this->existsElement("SELECT l.langISOId AS langISOId
										FROM LangISO AS l
										WHERE l.value = :langISO", 
									array(':langISO' => (string) $str),
									'langISOId',
									true);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see International/Country/Normalizer/Adapter/Db/Country_Normalizer_Adapter_Db_Interface#isSupportedCountry()
	 */
	public function getCountriesWhereLangISOIsSpoken($str)
	{
		$res = $this->getResultSet("SELECT GROUP_CONCAT(c.name) AS country
										FROM LangISO AS l
											INNER JOIN Country_r_LangISOs AS cl ON (l.langISOId = cl.langISOId)
											INNER JOIN Country AS c ON (cl.countryId = c.countryId)
										WHERE l.value = :langISO
										GROUP BY l.value",
									array(':langISO' => (string) $str));
		if (is_array($res)) {
			$countriesStr = $res[0]['country'];//the countries are returned as a concatenated ','
			$res = explode(',', $countriesStr);
		}
		return $res;//return it as an array of countries
	}
}