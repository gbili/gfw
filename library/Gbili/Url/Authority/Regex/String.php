<?php
namespace Gbili\Url\Authority\Regex;

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
	 * 
	 * @var unknown_type
	 */
	protected $defaultRegex = '([^:@.]+:[^:@.]+@)?([A-Za-z0-9.-]{5,})(?::(\d+))?$';
	
	/**
	 * 
	 * @var unknown_type
	 */
	protected $userInfo = '([^:@.]+:[^:@.]+@)';
	
	/**
	 * authority cant be optional
	 * @var unknown_type
	 */
	protected $host = '([A-Za-z0-9.-]{5,})';
	
	/**
	 * 
	 * @var unknown_type
	 */
	protected $port = '(?::(\d+))';

	/**
	 * Only called 
	 * 
	 * (non-PHPdoc)
	 * @see Common/Regex/Regex_String_Abstract#getUpdatedRegex()
	 */
	final protected function update()
	{
		$this->setRegex($this->userInfo . '?' . $this->host . $this->port . '?' . '$');
	}

}