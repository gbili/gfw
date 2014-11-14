<?php
namespace Gbili\Time\Length\StrToInt\Regex;

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
	protected $defaultRegex = '(?:(?:(\d+):)?(\d+):)?(\d\d)';

	/**
	 * Only called 
	 * 
	 * (non-PHPdoc)
	 * @see Common/Regex/Regex_String_Abstract#getUpdatedRegex()
	 */
	final protected function update()
	{
		$this->setRegex($this->defaultRegex);
	}

}