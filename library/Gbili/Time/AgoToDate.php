<?php
namespace Gbili\Time;

/**
 * 
 * @author gui
 *
 */
class AgoToDate
{
	/**
	 * @var unknown_type
	 */
	private $unixTimeStamp = null;
	
	/**
	 * 
	 * @param unknown_type $daysCount
	 * @return unknown_type
	 */
	public function __construct($input)
	{
		$a = new Ago($input);
		$hoursCountFromNow = $a->toString();

		if (!is_numeric($hoursCountFromNow)) {
			throw new Exception('Input must be a numeric string or integer');
		}
		$secsFromNow = Time::toSeconds( (integer) $hoursCountFromNow, Time::HOURS);
		$this->unixTimeStamp = time() - $secsFromNow; // unix time stam of input
	}
	
	/**
	 * 
	 * @param unknown_type $dateFormat
	 * @return unknown_type
	 */
	public function getDate($format = 'd-m-Y')
	{
		return date($format, $this->unixTimeStamp);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getUnixTimeStamp()
	{
		return $this->unixTimeStamp;
	}
	
}