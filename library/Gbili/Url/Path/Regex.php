<?php
namespace Gbili\Url\Path;

use Gbili\Regex\AbstractRegex,
    Gbili\Url\Authority\Path\Regex\String;

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
	public function getDirectory()
	{
		return $this->getMatches(1);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasFileName()
	{
		return $this->hasGroup(2);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getFileName()
	{
		return $this->getMatches(2);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasFileExtension()
	{
		return $this->hasGroup(3);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getFileExtension()
	{
		return $this->getMatches(3);
	}
}