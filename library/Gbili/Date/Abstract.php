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
abstract class Date_Abstract
{
	/**
	 * If there are parts in the outputOrder array
	 * that are missing in the regex result then try
	 * a better outputOrder array
	 * Otherwise throw an exception
	 * 
	 * @var unknown_type
	 */
	public static $adaptIfMissingPart = true;
	
	/**
	 * 
	 * @var unknown_type
	 */
	private $inputString;
	
	/**
	 * Contains the regex that validates
	 * the urlString input
	 * 
	 * @var Date_Regex_String_...
	 */
	private $regex;
	
	/**
	 * This tells getOut\OutputArray() how to rearrange
	 * the date parts
	 * 
	 * @var unknown_type
	 */
	private $outputOrder;
	
	/**
	 * This contains the parts in the right
	 * order, to be used by toString()
	 * 
	 * @var unknown_type
	 */
	private $outputArray;
	
	/**
	 * True when the regex is valid
	 * and there is something to output
	 * 
	 * @var unknown_type
	 */
	private $isValid;
	
	/**
	 * Whenever a part is edited this should be false
	 * it will be true when $this->isValid() is called
	 * @var unknown_type
	 */
	private $isValidated;
	
	/**
	 * This must be chaged by subclass
	 * if they want to force some parts to be present
	 * 
	 * @var unknown_type
	 */
	protected $requiredParts;
	
	/**
	 * 
	 * @var unknown_type
	 */
	protected $outputString;
	
		/**
	 * What separates date part in toString();
	 * 
	 * @var unknown_type
	 */
	protected $separator = '-';
	
	/**
	 * Populates the class members
	 * 
	 * @param unknown_type $url
	 * @return unknown_type
	 */
	public function __construct($inputString, $outputOrder = array(), $inputOrder = array())
	{
		$this->setRequiredParts();
		//force subclasses to implement setRequiredParts() correctly to avoid crash in getOut\OutputArray
		if (null === $this->requiredParts[Date_Regex_String::DAY] ||
			null === $this->requiredParts[Date_Regex_String::MONTH] ||
			null === $this->requiredParts[Date_Regex_String::YEAR]) {
			throw new Date_Exception('Error : dayRequired montRequired and yearRequired must be set' . print_r($this, true));
		}
		//set the default output order if not passed in param
		if (empty($outputOrder)) {//default order
			$outputOrder = array(Date_Regex_String::DAY,
								 Date_Regex_String::MONTH,
								 Date_Regex_String::YEAR);
		}
		$this->setOutputOrder($outputOrder);
		//populate the regex object and allow the user to set an input order from construction
		$this->setRegex(new Date_Regex($inputString, new Date_Regex_String(null, $inputOrder)));
	}
	
	/**
	 * This will tell getOut\OutputArray whether to throw exception
	 * or not when one of those is missing
	 * @return unknown_type
	 */
	abstract protected function setRequiredParts();
	
	/**
	 * 
	 * @param array $order
	 * @return unknown_type
	 */
	public function setOutputOrder(array $order)
	{
		if (empty($order)) {
			throw new Date_Exception('Error : the outputOrder cannot be empty');
		}
		$count = count($order);
		for ($i = 0; $i < $count; $i++) {
			if (!isset($order[$i])) {
				throw new Date_Exception('Error : the outputOrder is no wellformed, it must be serially indexed, given : ' . print_r($order, true));
			}
		}
		//ensure class required parts are in the outputOrder array
		foreach ($this->requiredParts as $partIdentifier => $required) {
			if (true === $required && !in_array($partIdentifier, $order)) {
				throw new Date_Exception('Error : the part is required from subclass and it is not available.');
			}
		}
		$this->outputOrder = $order;
		$this->isValidated = false;
		return $this;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getOutputOrder()
	{
		return $this->outputOrder;
	}
	
	/**
	 * It will be valid only if the instance it has 
	 * something to output
	 * 
	 * When this is not valid
	 * try to see if $this->getRegex()->isValid()
	 * is not valid either. to see where the error
	 * came from
	 * 
	 * @return unknown_type
	 */
	public function isValid()
	{
		//if is valid is not set or it is not validated
		if (null === $this->isValid || !$this->isValidated) {
			//if the regex is valid see if there is something in outputArray
			if ($this->getRegex()->match()) {
				//if the output array is empty (because the part required for output is not in the parts available from regex)
				$arr = $this->getOutputArray();
				$this->isValid = !empty($arr);
			} else { //otherwise this can't be valid either
				print_r($this->getRegex());
				$this->isValid = false;
			}
			$this->isValidated = true;
		}
		return $this->isValid;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function isValidated()
	{
		return $this->isValidated;
	}
	
	/**
	 * 
	 * @param $string
	 * @return unknown_type
	 */
	public function setSeparator($string)
	{
		$this->separator = (string) $string;
		return $this;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getSeparator()
	{
		return $this->separator;
	}
	
	/**
	 * If the class has something to output
	 * it will get the outputArray and render it
	 * as a string
	 * 
	 * @return unknown_type
	 */
	abstract public function toString();
	
	/**
	 * If the regex is not valid it throws up
	 * otherwise it will try to make the best possible
	 * outputArray given the available parts from the regex
	 * and the required parts by outputOrder
	 * 
	 * Whenever the outputOrder requires parts that are not
	 * available from regex it will adapt the outputOrder
	 * array by removing those parts (only when $adaptIfMissingPart === true)
	 * In that process of slicing the outputOrder array whenever
	 * a part is not available (with adaptOrThrowException())
	 * it may empty the outputOrder array (if none of the parts 
	 * available from regex match the parts required by outputOrder).
	 * If this case arises, then the outputArray will be empty
	 * 
	 * @return unknown_type
	 */
	public function getOutputArray()
	{
		//return if already available
		if (null !== $this->outputArray && true === $this->isValidated) {
			return $this->outputArray;
		}
		//ensure regex match succeed, throw exception otherwise
		if (!$this->getRegex()->match()) {
			throw new Date_Exception('Error : the date is not valid, or there was an error, use Data::isValid() method before calling this to avoid this error when it is not valid.' . print_r($this, true));
		}
		//prepare the output array that will be transformed to a string for return
		$this->outputArray = array();
		//ensure all parts required for output are available in regex
		while (list($partPos, $partIdentifier) = each($this->outputOrder)) {
			//if that part is not available from regex try to adapt the outputOrder (if allowed by user or design)
			if (null === $partValue = $this->getRegex()->getPart($partIdentifier)) {
				//!!!!important this will change the $this->outputOrder array that is why a while loop is used instead of a foreach
				$this->adaptOrThrowException($partPos);
			} else { //if the part is there then just add it to the outputArray
				$this->outputArray[$partPos] = $partValue;
			}
		}
		return $this->outputArray;
	}
	
	/**
	 * This will slice the part at $pos from the 
	 * outputOrder array
	 * 
	 * This may empty the outputOrder array
	 * it will cause getOut\OutputArray() to create
	 * an empty outputArray
	 * 
	 * @param unknown_type $pos
	 * @return unknown_type
	 */
	private function adaptOrThrowException($pos)
	{
		//if the user does not want to adapt when missing, or the part missing is required by design throw up
		if (false === self::$adaptIfMissingPart 					  ||
			true === $this->requiredParts[$this->outputOrder[$pos]]) {
			throw new Date_Exception('Error : the part that you required, is not available and it is not possible to adapt the output, if Date::$adaptIfMissingPart == false, you may want to change that. Date::$adaptIfMissingPart : ' . print_r(self::$adaptIfMissingPart, true));
		}
		//remove the portion of the outputOrder that corresponds to 
		//the part (so outputArray does not have that element)
		array_splice($this->outputOrder, $pos, 1);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getYear()
	{
		return $this->getRegex()->getYear();
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getMonth()
	{
		return $this->getRegex()->getMonth();
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getDay()
	{
		return $this->getRegex()->getDay();
	}
	
	/**
	 * 
	 * @param Regex_String_Abstract $regex
	 * @return unknown_type
	 */
	public function setRegex(Regex_Abstract $regex)
	{
		$this->regex = $regex;
		$this->isValidated = false;
		return $this;
	}
	
	/**
	 * This is set since construction
	 * 
	 * @return unknown_type
	 */
	public function getRegex()
	{
		return $this->regex;
	}

	/**
	 * Proxy
	 * 
	 * @return unknown_type
	 */
	public function getRegexString($iWillEdit = true)
	{
		$this->isValidated = !$iWillEdit;
		return $this->regex->getRegexStringObject((boolean) $iWillEdit);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getinputString()
	{
		return $this->getRegex()->getInputString();
	}
}