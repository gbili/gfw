<?php
namespace Gbili\Slug\Regex;

use Gbili\Regex\String\AbstractString;

/**
 * This class is will generate the regex pattern to match a date
 * There are a lot of things to rethink like the fact that the 3 parts
 * of the date must be set
 * 
 * 
 * @see Date_Regex for the implementation in a preg_match function
 * 
 * @author gui
 *
 */
class String 
extends AbstractString
{
	/**
	 * 
	 * @var unknown_type
	 */
	protected $defaultRegex = '^([A-Za-z0-9]+(?:[A-Za-z0-9-]+[A-Za-z0-9])?)$';
	
	/**
	 * Only called 
	 * 
	 * (non-PHPdoc)
	 * @see Common/Regex/Regex_String_Abstract#getUpdatedRegex()
	 */
	protected function update()
	{
	}
}