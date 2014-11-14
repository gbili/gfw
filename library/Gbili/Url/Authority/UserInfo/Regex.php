<?php
namespace Gbili\Url\Authority\UserInfo;

use Gbili\Regex\AbstractRegex,
    Gbili\Url\Authority\UserInfo\Regex\String;

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
	public function getName()
	{
		return $this->getMatches(1);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getPass()
	{
		return $this->getMatches(2);
	}
}