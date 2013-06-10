<?php
namespace Gbili;

/**
 * This class takes a dirty input string and matches
 * it against a regexString retrieved from a Date_Regex_String
 * instance, producing a matches array.
 * 
 * It then gives meaning to the matches with the help of Date_Regex_String
 * and provides handy methods like getMonth()
 * 
 * If a part is not available it returns null when calling getPart(...) 
 * 
 * @author gui
 *
 */
class Date_Regex
extends Regex_Abstract
{	
	/**
	 * 
	 * @param unknown_type $input
	 * @param unknown_type $regexStringObject
	 * @return unknown_type
	 */
	public function __construct($input, $regexStringObject = null)
	{
		parent::__construct($input, $regexStringObject);
	}
	
	/**
	 * 
	 * @param Date_Regex_String::const $partConst
	 * @return unknown_type
	 */
	public function getPart($partConst)
	{
		$matchesGroupsMap = $this->getRegexStringObject(false)->getMatchesGroupsMap();
		if (isset($matchesGroupsMap[$partConst])) {
			return $this->getMatches($matchesGroupsMap[$partConst]);
		} else {
			return;
		}
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getMonth()
	{
		return $this->getPart(Date_Regex_String::MONTH);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getYear()
	{
		return $this->getPart(Date_Regex_String::YEAR);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getDay()
	{
		return $this->getPart(Date_Regex_String::DAY);
	}
}