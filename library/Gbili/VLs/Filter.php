<?php
namespace Gbili\VLs;

class Filter
{
	/** 
	 * OrderBy
	 * 
	 * @var unknown_type
	 */
	const POPULARITY = 1;
	const DATE = 2;
	const NAME = 3;
	const TIME_LENGTH = 4;
	const RANDOM = 5;
	
	/**
	 * Restrict results by filtering
	 * the allowed content
	 * 
	 * No restrictions
	 * 
	 * @var unknown_type
	 */
	const SO_NO = 12;
	
	/**
	 * Hetero + Lesbian
	 * 
	 * @var unknown_type
	 */
	const SO_STRAIGHT = 13;
	
	/**
	 * Only men
	 * 
	 * @var unknown_type
	 */
	const SO_GAY = 14;
	
	/**
	 * hetero + bi + shemale
	 * 
	 * @var unknown_type
	 */
	const SO_OPENMINDED = 15;
	
	/**
	 * only shemale
	 * 
	 * @var unknown_type
	 */
	const SO_SHEMALE = 16;
	
	/**
	 * sexual orientation
	 * 
	 * @var unknown_type
	 */
	private static $so = null;
	
	/**
	 * Number of items returned by a get
	 * 
	 * @var unknown_type
	 */
	private static $defaultIPP = 6;
	
	/**
	 * num of items returned per page
	 * 
	 * @var unknown_type
	 */
	private $iPP = null;
	
	/**
	 * number of the page
	 * 
	 * @var unknown_type
	 */
	private $pageNum = 1;
	
	/**
	 * 
	 * @var unknown_type
	 */
	private $str = null;
	
	/**
	 * 
	 * @var unknown_type
	 */
	private $orderBy = null;
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function __construct($pageNum = null, $regex = null)
	{
		if (null !== $pageNum) {
			$this->setPageNum($pageNum);
		}

		if (null !== $regex) {
			$this->setStr($regex);
		}
		$this->orderBy = self::NAME;
	}
	
	/**
	 * 
	 * @param unknown_type $num
	 * @return unknown_type
	 */
	public function setPageNum($num)
	{
		$this->pageNum = (integer) $num;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getPageNum()
	{
		return $this->pageNum;
	}
	
	/**
	 * 
	 * @param unknown_type $regex
	 * @return unknown_type
	 */
	public function setStr($regex)
	{
		if (!is_string($regex)) {
			throw new VLs_Filter_Exception('regex should be a string');
		}
		$this->str = $regex;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasStr()
	{
		return null !== $this->str;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getStr()
	{
		if (false === $this->hasStr()) {
			throw new VLs_Filter_Exception('You are trying to get a string filter and it has not been set');
		}
		return $this->str;
	}
	
	/**
	 * 
	 * @param $num
	 * @return unknown_type
	 */
	public function setIPP($num)
	{
		$this->iPP = (integer) $num;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getIPP()
	{
		if (null === $this->iPP) {
			$this->iPP = self::$defaultIPP;
		}
		return $this->iPP;
	}
	
	/**
	 * The item number from which to start count
	 * @return unknown_type
	 */
	public function getStartItem()
	{
		return ($this->getIPP() * ($this->getPageNum() - 1));
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function orderBy($const)
	{
		$this->orderBy = $const;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getOrderBy()
	{
		return $this->orderBy;
	}
	
	/**
	 * 
	 * @param $num
	 * @return unknown_type
	 */
	public static function setDefaultIPP($num)
	{
		self::$defaultIPP = (integer) $num;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public static function getDefaultIPP()
	{
		return self::$defaultIPP;
	}
	
	/**
	 * sexual orientation
	 * 
	 * @param $const
	 * @return unknown_type
	 */
	public static function setSexO($const)
	{
		if (!in_array($const, array(self::SO_NO, self::SO_STRAIGHT, self::SO_GAY, self::SO_OPENMINDED, self::SO_SHEMALE))) {
			throw new VLS_Filter_Exception('sex orientation not supported');
		}
		self::$so = $const;
	}
	
	/**
	 * sexual orientaiton
	 * 
	 * @return unknown_type
	 */
	public static function getSexO()
	{
		if (null === self::$so) {
			self::$so = self::SO_NO;
		}
		return self::$so;
	}
}