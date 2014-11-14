<?php
namespace Gbili\Url\Authority\Host\Regex;

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
	protected $defaultRegex = '((?:[^.]+\.)*?)?([A-Za-z0-9][A-Za-z0-9\-]+[A-Za-z0-9])\.([A-Za-z]{2,4})(?::(\d+))?$';

	/**
	 * @var unknown_type
	 */
	protected $subdomains = '((?:[^.]*\.)*?)';
	
	/**
	 * @var unknown_type
	 */
	protected $subdomainsOptional = true;
	
	/**
	 * authority cant be optional
	 * @var unknown_type
	 */
	protected $sLDomain = '([A-Za-z0-9][A-Za-z0-9\-]+[A-Za-z0-9])';
	
	/**
	 * 
	 * @var unknown_type
	 */
	protected $tLDomain = '([A-Za-z]{2,4})';
	
	/**
	 * 
	 * @var unknown_type
	 */
	protected $port = '(?::(\d+))';

	/**
	 * 
	 * @var unknown_type
	 */
	protected $portOptional = true;

	/**
	 * Only called 
	 * 
	 * (non-PHPdoc)
	 * @see Common/Regex/Regex_String_Abstract#getUpdatedRegex()
	 */
	final protected function update()
	{
		if (true === self::$appendQuestionmarks) {
			$this->subdomains .=  ((boolean) $this->subdomainsOptional == true)? '?' : '';
			$this->port .=  ((boolean) $this->portOptional == true)? '?' : '';
		}
		$this->setRegex($this->subdomains . $this->sLDomain . '\.' .  $this->tLDomain . $this->port . '$');
	}

}