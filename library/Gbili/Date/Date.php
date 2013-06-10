<?php
namespace Gbili;

/**
 * This class is meant to get a string as input and return
 * a sanitized date
 * It uses Date_Regex to retrieve the date parts
 * from the string
 * 
 * @author gui
 *
 */
class Date_Date
extends Date_Abstract
{	
	/**
	 * Populates the class members
	 * 
	 * @param unknown_type $url
	 * @return unknown_type
	 */
	public function __construct($inputString, $outputOrder = array(), $inputOrder = array())
	{
		parent::__construct($inputString, $outputOrder, $inputOrder);
	}
	
	/**
	 * This will tell getOut\OutputArray whether to throw exception
	 * or not when one of those is missing
	 * @return unknown_type
	 */
	protected function setRequiredParts()
	{
		$this->requiredParts[Date_Regex_String::DAY]   = false;
		$this->requiredParts[Date_Regex_String::MONTH] = false;
		$this->requiredParts[Date_Regex_String::YEAR]  = false;
	}
	
	/**
	 * If the class has something to output
	 * it will get the outputArray and render it
	 * as a string
	 * 
	 * @return unknown_type
	 */
	public function toString()
	{
		//return if already available
		if (null !== $this->outputString && true === $this->isValidated()) {
			return $this->outputString;
		}
		//ensure there is something to output
		if (!$this->isValid()) {
			throw new Date_Exception('Error : there is nothing to output probably because the inputOrder and outputOrder have no common parts, use the isValid() function before the toString() $this :' . print_r($this, true));
		}
		//otherwise get the output array and turn it into string
		$outputArray = $this->getOutputArray();
		$count = count($outputArray);
		if ($count > 1) {
			//if we used implode instead of the for loop it wont order the keys so the outputOrder won't be respected
			$this->outputString = (string) $outputArray[0];
			for ($i = 1; $i < $count; $i++) {
				 $this->outputString .= $this->separator . $outputArray[$i];
			}
		} else { // == 1
			$this->outputString = (string) $outputArray[0];
		}
		return $this->outputString;
	}
}