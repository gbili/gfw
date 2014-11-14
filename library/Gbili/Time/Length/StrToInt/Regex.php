<?php
namespace Gbili\Time\Length\StrToInt;

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
	public function getHours()
	{
		return $this->getMatches(1);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasHours()
	{
		return $this->hasGroup(1);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getMinutes()
	{
		return $this->getMatches(2);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasMinutes()
	{
		return $this->hasGroup(2);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getSeconds()
	{
		return $this->getMatches(3);
	}
}