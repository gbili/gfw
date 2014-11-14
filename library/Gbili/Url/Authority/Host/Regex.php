<?php
namespace Gbili\Url\Authority\Host;

use Gbili\Regex\AbstractRegex,
    Gbili\Url\Authority\Host\Regex\String;

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
	public function hasSubdomains()
	{
		return $this->hasGroup(1);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getSubdomains()
	{
		return $this->getMatches(1);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getSLDomain()
	{
		return $this->getMatches(2);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getTLDomain()
	{
		return $this->getMatches(3);
	}
}