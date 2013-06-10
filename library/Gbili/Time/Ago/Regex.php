<?php
namespace Gbili\Time\Ago;

use Gbili\Regex\AbstractRegex;

class Regex
extends AbstractRegex
{
	/**
	 * 
	 * @param unknown_type $input
	 * @param Url_Regex_String $regexStringObject
	 * @return unknown_type
	 */
	public function __construct($input, Regex\String $regexStringObject)
	{
		parent::__construct($input, $regexStringObject);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getNumber()
	{
		return $this->getMatches(1);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasYears()
	{
		return $this->hasGroup(2);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasMonths()
	{
		return $this->hasGroup(3);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasDays()
	{
		return $this->hasGroup(4);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasHours()
	{
		return $this->hasGroup(5);
	}
}