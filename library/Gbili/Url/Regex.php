<?php
namespace Gbili\Url;

use Gbili\Regex\AbstractRegex,
    Gbili\Url\Regex\String;

class Regex
extends AbstractRegex
{
    
    private static $defaultScheme = 'http';
    
	/**
	 * 
	 * @param unknown_type $input
	 * @param Url_Regex_String $regexStringObject
	 * @return unknown_type
	 */
	public function __construct($input, String $regexStringObject)
	{
		parent::__construct($input, $regexStringObject);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getScheme()
	{
		return ($this->hasGroup(1))? $this->getMatches(1) : self::$defaultScheme;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getAuthority()
	{
		return mb_strtolower($this->getMatches(2));
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasPath()
	{
		return $this->hasGroup(3);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getPath()
	{
		return $this->getMatches(3);
	}
}