<?php
namespace Gbili\Url\Authority;

use Gbili\Regex\AbstractRegex,
    Gbili\Url\Authority\Regex\String;

class Regex
extends AbstractRegex
{
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
	public function hasUserInfo()
	{
		return $this->hasGroup(1);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getUserInfo()
	{
		return $this->getMatches(1);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getHost()
	{
		return $this->getMatches(2);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasPort()
	{
		return $this->hasGroup(3);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getPort()
	{
		return $this->getMatches(3);
	}
}