<?php
namespace Gbili\Country\Normalizer\Adapter;

use Gbili\Db\Registry as DbRegistry;

/**
 * 
 * @author gui
 *
 */
class Db
extends AbstractAdapter
{	
	/**
	 * Retrieves all records at once
	 * 
	 * @var unknown_type
	 */
	const MODE_FETCH_ALL = 1234;
	
	/**
	 * Retrieves the records in block
	 * the size depends on the value of
	 * self::$maxRowsBeforeFractionning;
	 * 
	 * @var unknown_type
	 */
	const MODE_FETCH_BLOCKS = 2345;
	
	/**
	 * Query db each time getNext() is
	 * called
	 * 
	 * @var unknown_type
	 */
	const MODE_FETCH_ONE = 3456;
	
	/**
	 * Tells the number of rows that
	 * a query can retrieve when
	 * self::$mode == MODE_FETCH_BLOCKS
	 * @var unknown_type
	 */
	public static $blockMaxSize = 15;
	
	/**
	 * The mode in which this class
	 * fetches the databse
	 * 
	 * @var unknown_type
	 */
	private static $mode;

	/**
	 * Tells the number of queries
	 * that have been made against db
	 * 
	 * @var unknown_type
	 */
	private $fetchCount;
	
	/**
	 * 
	 * @var unknown_type
	 */
	private $db;

	/**
	 * Contains the db array of
	 * the country matchers
	 * ex : 
	 * 	array(1=>array('name' = 
	 * 					)
	 * 	)
	 * 
	 * @var unknown_type
	 */
	private $countryMatchersArray;

	/**
	 * 
	 * @return unknown_type
	 */
	public function __construct()
	{
		parent::__construct();
		$this->db = DbRegistry::getInstance(__NAMESPACE__);
		if (null === self::$mode) {
			self::$mode = self::MODE_FETCH_BLOCKS;
		}
		$this->reset();
	}
	
	/**
	 * 
	 * @param unknown_type $countryStr
	 * @return unknown_type
	 */
	public function saveDirtyCountryStr($countryStr)
	{
		if (false === $id = $this->db->saveDirtyCountryStr($countryStr)) {
			throw new Exception('No id returned by save dirty countr str' . $countryStr);
		}
		return $id;
	}
	
	/**
	 * 
	 * 
	 * This function can be implemented
	 * in many different ways:
	 * 	1. query the database once
	 * 	   and retrieve all the records
	 *     then get next would only loop
	 *     through the retrieved records
	 *     until Abstract finds the match
	 * 	
	 * 	2. query the database for a
	 *     first set of common countries
	 *     and loop through them, if the
	 *     Abstract class doesn't find a
	 *     match, the query a second set,
	 *     do this until it matches
	 *     
	 *  3. query the database each thime
	 *     Abstract class wants a new 
	 *     record.
	 * 
	 * with in a way where there may be
	 * queries 
	 * 
	 * @return unknown_type
	 */
	public function getNext()
	{
		//if there are no elements in array
		if (empty($this->countryMatchersArray)) {
			// fetch elements from db
			$this->fillCMArray();
			//if there is nothing more in database
			if (false === $this->countryMatchersArray) {
				//let abstract know that there are no more
				return false;
			}
		}
		//countrymatchersarray is not false so it didn't return
		//as it is not false it may contain an array
		//however if pointer in array reached end
		if (false === list($key, $element) = each($this->countryMatchersArray)) {
			//fetch elements from db
			$this->fillCMArray();
			//if there is nothing more in database
			if (false === $this->countryMatchersArray) {
				//let abstract know that there are no more
				return false;
			}
			//if reached here, there is something in array
			//and the pointer is different than null
			list($key, $element) = each($this->countryMatchersArray);
		}
		//reformat db row to abstract understandable
		return $this->reformatRow($element);
	}

	/**
	 * Will put an array or false in $countryMatchersArray
	 * 
	 * @return unknown_type
	 */
	private function fillCMArray()
	{
		//adapt query to mode
		switch  (self::$mode) {
			case self::MODE_FETCH_BLOCKS;
					$this->countryMatchersArray = $this->db->getBlock($this->fetchCount);
				break;
			case self::MODE_FETCH_ALL;
					$this->countryMatchersArray = $this->db->getAll();
				break;
			case self::MODE_FETCH_ONE;
					$this->countryMatchersArray = $this->db->getOne($this->fetchCount);
				break;
			default;
					throw new Exception('Error : You must also put case in switch for getSql() when adding a new mode or use the generic mode MODE_FETCH_ALL if you don\'t need to edit the sql.');
				break;
		}
		if ($this->countryMatchersArray !== false){//there are results from query
			//increase fetch count
			$this->fetchCount++;
		}
	}
	
	/**
	 * Converts the database row array
	 * to a abstract understandable array
	 * of a country matcher
	 * 
	 * @param array $rowArray
	 * @return unknown_type
	 */
	public function reformatRow(array $rowArray)
	{
		$countryName = array_shift($rowArray);
		$return = array();
		$return[$countryName] = array('regex' 	=> $rowArray['regex'],
						  			  'langISO' => explode(',', $rowArray['langISO']),
									  'id'		=> $rowArray['countryId']);
		return $return;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see International/Country/Normalizer/Adapter/Country_Normalizer_Adapter_Abstract#reset()
	 */
	public function reset()
	{
		$this->countryMatchersArray = array();
		$this->fetchCount = 0;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see International/Country/Normalizer/Adapter/Country_Normalizer_Adapter_Abstract#getCountries()
	 */
	public function getCountries()
	{
		if (false === $countriesArray = $this->db->getCountries()) {
			throw new Exception('Error : Db does not have any country');
		}
		return $countriesArray;
	}
	
	/**
	 * 
	 * (non-PHPdoc)
	 * @see International/Country/Normalizer/Adapter/Country_Normalizer_Adapter_Abstract#getLangISOFromNormalizedCountry($normalizedCountryStr)
	 */
	public function getLangISOFromNormalizedCountry($countryStr)
	{
		$langsStr = $this->db->getCountryLangISO($countryStr);
		//if there are no results
		if (false === $langsStr) {
			//ensure the country is normalized|supported
			if ($this->db->isSupportedCountry($countryStr)) {
				throw new Exception('Error : the passed country str apears not to be normalized or the country is not supported given :' . print_r($countryStr, true));
			}
		}
		return explode(',', $langsStr);//'en,fr,de' -> array('en', 'fr', 'de')
	}
	
	/**
	 * (non-PHPdoc)
	 * @see International/Country/Normalizer/Adapter/Country_Normalizer_Adapter_Abstract#isNormalizedCountryStr($dirtyCountryStr)
	 */
	public function isNormalizedCountryStr($dirtyCountryStr)
	{
		return $this->db->isSupportedCountry($dirtyCountryStr);
	}
	
	/**
	 * Set the way this class will fetch database
	 * 
	 * @param $mode
	 * @return unknown_type
	 */
	public static function setMode($mode)
	{
		//ensure mode is supported
		switch  ($mode) {
			case self::MODE_FETCH_BLOCKS;
				break;
			case self::MODE_FETCH_ALL;
				break;
			case self::MODE_FETCH_ONE;
				break;
			default;
					throw new Exception('Error : The mode is not supported, given: ' . print_r($mode,true));
				break;
		}
		self::$mode = $mode;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public static function getMode()
	{
		return self::$mode;
	}
}