<?php
namespace Gbili\Value;

/**
 * 
 * @author gui
 *
 */
class Savable
extends \Gbili\Savable\Savable
{
	/**
	 * 
	 * @param unknown_type $value
	 * @return unknown_type
	 */
	public function __construct($value)
	{
		parent::__construct();
		$this->setRequestorClassname(__CLASS__);
		$this->setPassTableNameToRequestor();
		$this->setElement('value', $value);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getValue()
	{
		return $this->getElement('value');
	}
}