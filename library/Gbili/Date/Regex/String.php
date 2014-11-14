<?php
namespace Gbili;

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
class Date_Regex_String
extends Regex_String_Abstract
{
	/**
	 * these numbers corresponds to the position
	 * of parts in self::$originalParts
	 * 
	 * @var unknown_type
	 */
	const YEAR  = 0; 
	const MONTH = 1;
	const DAY	= 2;
	
	/**
	 * The parts are implemented as an array to
	 * ease reorganisation
	 * Never touch this array order it is related to
	 * the class constants and used in reorderParts()
	 * 
	 * @var unknown_type
	 */
	private static $originalParts = array('(?:1[0-9]|20)\d\d',//year
										   '0[1-9]|1[012]',//month
										   '0[1-9]|[12][0-9]|3[01]');//day

	/**
	 * Contains the very default regex
	 * 
	 * @var unknown_type
	 */
	protected $defaultRegex = '((?:19|20)\d\d)(?:([- /.])(0[1-9]|1[012])\2(0[1-9]|[12][0-9]|3[01]))?';

	/**
	 * The parts reorganized in
	 * the desired order
	 * 
	 * @var unknown_type
	 */
	private $parts = array();
	
	/**
	 * By default all parts are required
	 * 
	 * @var unknown_type
	 */
	private $optionalParts = array(false, false, false);
	
	/**
	 * Contains the order of the regex parts
	 * 
	 * @var unknown_type
	 */
	private $order;
	
	/**
	 * Contains the cardinality of $order
	 * 
	 * @var unknown_type
	 */
	private $orderCount;
	
	/**
	 * 
	 * @var unknown_type
	 */
	private $separator = '[- /.]';
	
	/**
	 * Maps each part to its group in the
	 * matches array of the preg match
	 * 
	 * @var unknown_type
	 */
	private $matchesGroupsMap;
	
	/**
	 * 
	 * @param unknown_type $regex
	 * @return unknown_type
	 */
	public function __construct($regex = null, array $order = array())
	{
		parent::__construct($regex);
		//allow reordering of parts
		if (empty($order)) {
			//if no reordering is needed set the order to default
			$this->order = array(self::YEAR, self::MONTH, self::DAY);
			$this->orderCount = 3;
			//this works because $original parts keys are the same as constants
			$this->parts = self::$originalParts;
		} else {
			$this->setOrder($order);
		}
		//if the order count is < 2 then set the parts that are not in order to optional
		//if ($this->orderCount) {
			
		//}
		
	}
	
	/**
	 * This allows some parts to be optional
	 * 
	 * @param array(Date_Regex_String::PART=> true, Date_Regex_String::PART=> false etc.) $part
	 * @return unknown_type
	 */
	public function setOptionalParts(array $parts)
	{
		$optionalCount = 0;//will contain the number of parts that are set to optional
		foreach ($parts as $key => $value) {
			if (!isset($this->parts[$key])) {
				throw new Date_Regex_String_Exception('Error : you cannot define optionality on missing parts.');
			}
			$this->optionalParts[$key] = (boolean) $value;
			if (true == $value) {
				$optionalCount++;//count the number of parts that are optional
			}
		}
		//if the number of optional parts is greater or the same as the number of parts, throw up
		if (count($parts) <= $optionalCount) {
			throw new Date_Regex_String_Exception('Error : all your parts cannot be optional, there must at least be one not optional given : ' . print_r($this->partsOptional,true));
		}
		$this->setRegexStringAsNotUpToDate();
		return $this;
	}
	
	/**
	 * Use this if you want for example a
	 * month in letters rather than in digits
	 * 
	 * @param unknown_type $month
	 * @return unknown_type
	 */
	public function setMonth($regex)
	{
		$this->parts[self::MONTH] = (string) $regex;
		$this->setRegexStringAsNotUpToDate();
		return $this;
	}
	
	/**
	 * 
	 * @param unknown_type $day
	 * @return unknown_type
	 */
	public function setDay($regex)
	{
		$this->parts[self::DAY] = (string) $regex;
		$this->setRegexStringAsNotUpToDate();
		return $this;
	}
	
	/**
	 * 
	 * @param unknown_type $year
	 * @return unknown_type
	 */
	public function setYear($regex)
	{
		$this->parts[self::YEAR]= (string) $regex;
		$this->setRegexStringAsNotUpToDate();
		return $this;
	}
	
	/**
	 * Change the separator
	 * 
	 * @param unknown_type $separator
	 * @return unknown_type
	 */
	public function setSeparator($separator)
	{
		$this->separator = (string) $separator;
		$this->setRegexStringAsNotUpToDate();
		return $this;
	}
	
	/**
	 * This is the order of the regex groups
	 * !!! Don't change the newOrder checks otherwise rest will crash
	 * 
	 * @param array $order
	 * ex : array(self::DAY, self::MONTH, self::YEAR);
	 * @return unknown_type
	 */
	public function setOrder(array $newOrder)
	{
		//only one element is required
		if (!isset($newOrder[0]) || $newOrder[0] > 2) {
			throw new Regex_String_Exception('Error : reordering is not possible, because the param array is not well formed, expecting array(Date_Regex_String::DAY,Date_Regex_String::Date_Regex_String::YEAR) or something like that.. given : ' . print_r($newOrder, true));
		}
		$count = count($newOrder);
		//the new order array cant be out of these bindings
		if ($count > 3 || $count === 0) {
			throw new Date_Regex_String_Exception('Error : $newOrder cannot have more elements than 3 or 0 given : ' . print_r($newOrder, true));
		}
		//from the order set the parts array
		//from now on the parts array only contains what is defined in order
		$partsCopy = $this->parts;
		$this->parts = array(); //empty the parts array
		for ($i = 0; $i < $count; $i++) {
			//ensure order is serially indexed
			if (!isset($newOrder[$i])) {
				throw new Date_Regex_String_Exception('Error : $newOrder must be serially indexed : ' . print_r($newOrder, true));
			}
			//now set the parts array
			if (isset($partsCopy[$newOrder[$i]])) {//try to get the latest one
				$this->parts[$newOrder[$i]] = $partsCopy[$newOrder[$i]];
			} else {//if not available retrieve the default one
				$this->parts[$newOrder[$i]] = self::$originalParts[$newOrder[$i]];
			}	
		}
		$this->order = $newOrder;
		$this->orderCount = $count;
		//force regex to update itself
		$this->setRegexStringAsNotUpToDate();
		return $this;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getOrder()
	{
		return $this->order;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getOrderCount()
	{
		return $this->orderCount;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getMatchesGroupsMap()
	{
		if (!$this->isRegexStringUpToDate()) {
			$this->update();
		}
		return $this->matchesGroupsMap;
	}

	/**
	 * Only called when getRegex() is called and it is not up to date
	 * 
	 * (non-PHPdoc)
	 * @see Common/Regex/Regex_String_Abstract#getUpdatedRegex()
	 */
	protected function update()
	{
		$orderedParts = array();
		foreach ($this->order as $partKey) {
			$orderedParts[] = (true === $this->optionalParts[$partKey])? '(' . $this->parts[$partKey] . ')?' : '(' . $this->parts[$partKey] . ')';
		}
		switch ($this->orderCount) {
			case 1;
				$regex = $orderedParts[0];
				$this->matchesGroupsMap[$this->order[0]] = 1;
				break;
			case 2;
				$regex = $orderedParts[0] . $this->separator . $orderedParts[1];
				$this->matchesGroupsMap[$this->order[0]] = 1;
				$this->matchesGroupsMap[$this->order[1]] = 2;
				break;
			case 3;
				$regex = $orderedParts[0] . "($this->separator)" . $orderedParts[1] . '\2' . $orderedParts[2];	
				$this->matchesGroupsMap[$this->order[0]] = 1;
				$this->matchesGroupsMap[$this->order[1]] = 3;
				$this->matchesGroupsMap[$this->order[2]] = 4;
				break;
			default;
				throw new Date_Regex_String_Exception('Error : programming error, the count should be 1,2 or 3. It has somehow made its way being different than that, order given : ' . print_r($this, true));
				break;
		}
		$this->setRegex($regex);
	}
}