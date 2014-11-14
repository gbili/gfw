<?php
namespace Gbili\Url\Regex;

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
	protected $defaultRegex = '(?:(https?|s?ftp)://)?([A-Za-z0-9.@:-]{5,})(?:(/[^ ]*)|$)';
	
	/**
	 * 
	 * @var unknown_type
	 */
	protected $scheme = '(?:(https?|s?ftp)://)';
	
	/**
	 * authority cant be optional
	 * @var unknown_type
	 */
	protected $authority = '([A-Za-z0-9.@:-]{5,})';
	
	/**
	 * 
	 * @var unknown_type
	 */
	protected $path = '(/[^ ]*)';

	/**
	 * Only called 
	 * 
	 * (non-PHPdoc)
	 * @see Common/Regex/Regex_String_Abstract#getUpdatedRegex()
	 */
	final protected function update()
	{
		$this->setRegex( $this->scheme . '?' . $this->authority . '(?:$|' . $this->path . ')');
	}

}