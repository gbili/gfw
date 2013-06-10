<?php
namespace Gbili\Url\Authority\UserInfo\Regex;

use Gbili\Regex\String\AbstractString;

/**
 * This class will create a url regular expression
 * 
 * When extending this class for specific urls remember
 * not to put optional capturing groups with a ? rather
 * set the specific part of the url ex : <subdomains>
 * to optional with : $subdomainsOptional = true
 * or override $appendQuestionmarks = false
 * 
 * What is the usfulness of this class?? : i dont see it
 * 
 * @author gui
 *
 */
class String 
extends AbstractString
{
	/**
	 * If you dont want to append question marks set this to false
	 * @var unknown_type
	 */
	public static $appendQuestionmarks = true;

	/**
	 * 
	 * @var unknown_type
	 */
	protected $defaultRegex = '([0-9A-Za-z]+):([0-9A-Za-z]+)@';
	
	/**
	 * @var unknown_type
	 */
	protected $name = '([0-9A-Za-z]+)';
	
	/**
	 * @var unknown_type
	 */
	protected $pass = '([0-9A-Za-z]+)';
	
	/**
	 * Only called 
	 * 
	 * (non-PHPdoc)
	 * @see Common/Regex/Regex_String_Abstract#getUpdatedRegex()
	 */
	final protected function update()
	{
		$this->setRegex($this->name . ':' . $this->pass . '@');
	}

}