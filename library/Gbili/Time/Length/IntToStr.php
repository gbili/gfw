<?php
namespace Gbili\Time\Length;



/**
 * Not finished :/
 * @author gui
 *
 */
class IntToStr
{
	/**
	 * 
	 * @var unknown_type
	 */
	private $hours = null;
	
	/**
	 * 
	 * @var unknown_type
	 */
	private $minutes = null;
	
	/**
	 * 
	 * @var unknown_type
	 */
	private $seconds = null;
	
	/**
	 * 
	 * @param unknown_type $input
	 * @return unknown_type
	 */
	public function __construct($input)
	{
		if (!is_numeric($input)) {
			throw new Exception('Input must be numeric');
		}
		$input = (string) $input;
		$this->seconds = mb_substr($input, -2);
		if (2 < $l = mb_strlen($input)) {
			if ($l === 3) {
				$this->minutes = mb_substr($input, -3, 1);
			} else {
				$this->minutes = mb_substr($input, -4, 2);
			}
			if (mb_strlen($input) > 4) {
				$this->hours = mb_substr($input, 0, -4);
			}
		}
	}

	/**
	 * 
	 * @return unknown_type
	 */
	public function hasHours()
	{
		return null !== $this->hours;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getHours()
	{
		return $this->getPart('hours');		
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getMinutes()
	{
		return $this->minutes;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasMinutes()
	{
		return null !== $this->seconds;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getSeconds()
	{
		return $this->seconds;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function toString()
	{
		return (($this->hasHours())? $this->getHours() . ':' : '') . (($this->hasMinutes())? $this->getMinutes() . ':': '') . $this->getSeconds();
	}
}