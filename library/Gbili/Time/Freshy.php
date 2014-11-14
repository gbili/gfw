<?php
namespace Gbili\Time;

/**
 * This class transforms
 * a timestamp to a more friendly
 * tag depending on the timestamp
 * elderness
 * 
 * @author gui
 *
 */
class Freshy
{
	/**
	 * map each self::$states key
	 * to a timelaps in secs
	 * @var unknown_type
	 */
	private static $statesLaps = array(Time::UNIXTS_DAY,
										Time::UNIXTS_WEEK,
										Time::UNIXTS_MONTH,
										Time::UNIXTS_QUARTER,
										Time::UNIXTS_SEMESTER,
										Time::UNIXTS_YEAR);
	/**
	 * Map each key to a tag
	 * 
	 * @var unknown_type
	 */
	private static $states = array('just shot', 'kiosk' ,'couch' ,'closet', 'garage', 'dusty');
	
	/**
	 * The constructor timestamp
	 * tag
	 * 
	 * @var unknown_type
	 */
	private $state = null;
	
	
	public function __construct($unixTimestamp)
	{
		if (!is_numeric($unixTimestamp)) {
			throw new Time_Exception('unix timestamp must be numeric, given : ' . print_r($unixTimestamp, true));
		}
		$elapsedSeconds = time() - (integer) $unixTimestamp;
		if (0 > $elapsedSeconds) {
			throw new Exception('unixtimestam must reference time from the past : ' . print_r($unixTimestamp, true));
		}
		
		foreach (self::$statesLaps as $k => $secs) {
			if ($elapsedSeconds < $secs ) {
				$this->state = self::$states[$k];
				break;
			}
		}
		if (null === $this->state) {
			$this->state = self::$states[5];
		}
	}
	
	/**
	 * 
	 * @param array $intervalsArray
	 * @return unknown_type
	 */
	public static function setIntervals(array $intervalsArray)
	{
		
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getState()
	{
		return $this->state;
	}
}