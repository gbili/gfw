<?php
namespace Gbili\Url\Path\Regex;

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
	protected $defaultRegex = '^(/(?:[^/]+/)*?)(?:([A_Za-z0-9\-_]+)\.([A-Za-z]{1,4}))?';
	
	/**
	 * 
	 * @var unknown_type
	 */
	protected $directory = '^(/(?:[^/]+/)*?)';
	
	/**
	 * 
	 * @var unknown_type
	 */
	protected $fileName = '([A_Za-z0-9\-_]+)';
	
	/**
	 * 
	 * @var unknown_type
	 */
	protected $fileExtension = '([A-Za-z]{1,4})';

	/**
	 * Only called 
	 * 
	 * (non-PHPdoc)
	 * @see Common/Regex/Regex_String_Abstract#getUpdatedRegex()
	 */
	final protected function update()
	{
		$this->setRegex( $this->directory . '(?:' . $this->fileName . '\.' . $this->fileExtension . ')?');
	}

}