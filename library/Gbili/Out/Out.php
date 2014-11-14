<?php
namespace Gbili\Out;

/**
 * This class is meant to allow to turn on
 * and off the output of a call to echo.
 * 
 * You can put different active states to
 * each function.
 * 
 * This allows you to treat a series of 
 * Out\Out::l1(in a different way if you use
 * different level<Num>() function to echo
 * 
 * 
 * @author gui
 *
 */
class Out
{
	/**
	 * Level number mapped to activation state
	 * 
	 * @var unknown_type
	 */
	private static $levels = array(1 => true, 2 => true, 3 => false, 4 => false, 5 => false);
	
	/**
	 * 
	 * @param $toOuting
	 * @param $levelNum
	 * @return unknown_type
	 */
	private static function mother($toOut, $levelNum, $EOL = true)
	{
		if (true === self::$levels[$levelNum]) {
			if (is_string($toOut)) {
				if (true === $EOL) {
					$toOut .= "\n";
				}
				echo $toOut;
			} else {
				print_r($toOut);
			}
		}
	}
	
	/**
	 * 
	 * @param unknown_type $level
	 * @return unknown_type
	 */
	public static function mute($level = null)
	{
		self::switchVolume('mute', $level);
	}
	
	/**
	 * 
	 * @param unknown_type $level
	 */
	public static function loud($level = null)
	{
	    self::switchVolume('loud', $level);
	}
	
	/**
	 * 
	 * @param unknown_type $toggle
	 * @param unknown_type $level
	 */
	public static function switchVolume($toggle = 'mute', $level = null)
	{
	    $action = ('mute' === $toggle)? false : true;
	    
	    if (null !== $level) {
	        if (is_array($level)) {
	            foreach ($level as $l) {
	                self::changeLevelState($l, $action);
	            }
	        } else {
	            self::changeLevelState($level, $action);
	        }
	    } else { //de|activate all levels
	        for ($i = 1; $i <= 5; $i++) {
	            self::$levels[$i] = $action;
	        }
	    }
	}

	/**
	 * 
	 * @param unknown_type $levelNum
	 * @return unknown_type
	 */
	public static function activateLevel($levelNum)
	{
		self::changeLevelState($levelNum, true);
	}

	/**
	 * 
	 * @param unknown_type $levelNum
	 * @return unknown_type
	 */
	public static function deactivateLevel($levelNum)
	{
		self::changeLevelState($levelNum, false);
	}
	
	/**
	 * 
	 * @param unknown_type $levelNum
	 * @param unknown_type $activateBool
	 * @return unknown_type
	 */
	private static function changeLevelState($levelNum, $activateBool)
	{
		self::throwIfNotExistsLevel($levelNum);
		self::$levels[(integer) $levelNum] = $activateBool;
	}
	
	/**
	 * 
	 * @param unknown_type $levelNum
	 * @return unknown_type
	 */
	public static function isActiveLevel($levelNum)
	{
		self::throwIfNotExistsLevel($levelNum);
		return self::$levels[(integer) $levelNum];
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	private static function throwIfNotExistsLevel($levelNum)
	{
		if (!is_numeric($levelNum)) {
			throw new Exception('the level specified must be numeric');	
		}

		if (!isset(self::$levels[(integer) $levelNum])) {
			throw new Exception('the level specified does not exist, add a function that supports it, given : ' . $levelNum);
		}
	}
	
	/**
	 * 
	 * @param unknown_type $toOut
	 * @return unknown_type
	 */
	public static function l1($toOut, $EOL = true)
	{
		self::mother($toOut, 1, $EOL);
	}
	
	/**
	 * 
	 * @param $toOut
	 * @return unknown_type
	 */
	public static function l2($toOut, $EOL = true)
	{
		self::mother($toOut, 2, $EOL);
	}
	
	/**
	 * 
	 * @param unknown_type $toOut
	 * @return unknown_type
	 */
	public static function l3($toOut, $EOL = true)
	{
		self::mother($toOut, 3, $EOL);
	}
	
	/**
	 * 
	 * @param unknown_type $toOut
	 * @return unknown_type
	 */
	public static function l4($toOut, $EOL = true)
	{
		self::mother($toOut, 4, $EOL);
	}
	
	/**
	 * 
	 * @param $toOut
	 * @return unknown_type
	 */
	public static function l5($toOut, $EOL = true)
	{
		self::mother($toOut, 5, $EOL);
	}
}