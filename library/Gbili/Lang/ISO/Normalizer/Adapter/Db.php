<?php
namespace Gbili\Lang\ISO\Normalizer\Adapter;

use Gbili\Db\Registry as DbRegistry;

/**
 * This thing is f* the same as Country... just some stupid name changes
 * What about abstraction?
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
	const MODE_FETCH_ALL = 12;
	
	/**
	 * Retrieves the records in block
	 * the size depends on the value of
	 * self::$maxRowsBeforeFractionning;
	 * 
	 * @var unknown_type
	 */
	const MODE_FETCH_BLOCKS = 23;
	
	/**
	 * Query db each time getNext() is
	 * called
	 * 
	 * @var unknown_type
	 */
	const MODE_FETCH_ONE = 34;
	
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
	 * the langISO matchers
	 * ex : 
	 * 	array(1=>array('langISO' = 
	 * 					)
	 * 	)
	 * 
	 * @var unknown_type
	 */
	private $langISOMatchersArray;

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
		if (empty($this->langISOMatchersArray)) {
			// fetch elements from db
			$this->fillLMArray();
			//if there is nothing more in database
			if (false === $this->langISOMatchersArray) {
				//let abstract know that there are no more
				return false;
			}
		}
		//countrymatchersarray is not false so it didn't return
		//as it is not false it may contain an array
		//however if pointer in array reached end
		if (false === list($key, $element) = each($this->langISOMatchersArray)) {
			//fetch elements from db
			$this->fillLMArray();
			//if there is nothing more in database
			if (false === $this->langISOMatchersArray) {
				//let abstract know that there are no more
				return false;
			}
			//if reached here, there is something in array
			//and the pointer is different than null
			list($key, $element) = each($this->langISOMatchersArray);
		}
		//reformat db row to abstract understandable
		return $this->reformatRow($element);
	}

	/**
	 * Will put an array or false in $langISOMatchersArray
	 * 
	 * @return unknown_type
	 */
	private function fillLMArray()
	{
		//adapt query to mode
		switch  (self::$mode) {
			case self::MODE_FETCH_BLOCKS;
					$this->langISOMatchersArray = $this->db->getBlock($this->fetchCount);
				break;
			case self::MODE_FETCH_ALL;
					$this->langISOMatchersArray = $this->db->getAll();
				break;
			case self::MODE_FETCH_ONE;
					$this->langISOMatchersArray = $this->db->getOne($this->fetchCount);
				break;
			default;
					throw new Exception('Error : You must also put case in switch for getSql() when adding a new mode or use the generic mode MODE_FETCH_ALL if you don\'t need to edit the sql.');
				break;
		}
		if ($this->langISOMatchersArray !== false){//there are results from query
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
		$return = array();
		$return[$rowArray['langISO']] = array('regex' => $rowArray['regex'], 
											  'id'    => $rowArray['langISOId']);
		return $return;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see International/Country/Normalizer/Adapter/Country_Normalizer_Adapter_Abstract#reset()
	 */
	public function reset()
	{
		$this->langISOMatchersArray = array();
		$this->fetchCount = 0;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see International/LangISO/Normalizer/Adapter/Lang_ISO_Normalizer_Adapter_Abstract#getLangISOs()
	 */
	public function getLangISOs()
	{
		if (false === $langISOsArray = $this->db->getLangISOs()) {
			throw new Exception('Error : Db does not have any langISOs');
		}
		return $langISOsArray;
	}

	/**
	 * 
	 * @param $lang
	 * @return unknown_type
	 */
	public function getCountriesWhereLangISOIsSpoken($lang)
	{
		$res = $this->db->getCountriesWhereLangISOIsSpoken($lang);
		//if there are no results
		if (false === $res) {
			//ensure the country is normalized|supported
			if (false === $this->db->isSupportedLangISO($lang)) {
				throw new Exception('Error : the passed langISO str appears not to be normalized given :' . print_r($countryStr, true));
			} else {//no country speaks this language wierd
				throw new Exception('The lang appears not to be spoken in any country ' . print_r($countryStr, true));
			}
		}
		return $res; //array('usa', 'england') || false
	}

	/**
	 * 
	 * @param $dirtyCountryStr
	 * @return unknown_type
	 */
	public function isNormalizedLangISOStr($lang)
	{
		return $this->db->isSupportedLangISO($lang);
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