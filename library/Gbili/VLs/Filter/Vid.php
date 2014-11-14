<?php
namespace Gbili\VLs\Filter;

use Gbili\VLs\Filter as Filter;

/**
 * Extend the parent functionality by 
 * adding a category filter
 * 
 * @author gui
 *
 */
class Vid
extends Filter
{
	/**
	 * @var unknown_type
	 */
	private $catSlug = null;
	
	/**
	 * 
	 * @var unknown_type
	 */
	private static $defaultIPP = 50;
	
	public function __construct($pageNum = null, $regex = null)
	{
		parent::__construct($pageNum, $regex);
		$this->iPP = self::$defaultIPP;
		$this->orderBy = Filter::DATE;
	}
	
	/**
	 * 
	 * @param int $n
	 * @return unknown_type
	 */
	public static function setDefaultIPP($n)
	{
		self::$defaultIPP = (integer) $n;
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
	 * 
	 * @param Slug $c
	 * @return unknown_type
	 */
	public function setCatSlug($c)
	{
		if (false === Slug::isSlug($c)) {
			throw new Exception('the catSlug must be a valid slug string');
		}
		$this->catSlug = $c;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasCatSlug()
	{
		return null !== $this->catSlug;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getCatSlug()
	{
		return $this->catSlug;
	}
	
}