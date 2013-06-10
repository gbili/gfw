<?php
namespace Gbili\Time;

use Gbili\Regex\Encapsulator\AbstractEncapsulator;

class Ago
extends AbstractEncapsulator
{
	
	/**
	 * (non-PHPdoc)
	 * @see Url/Url_Abstract#toString()
	 */
	protected function partsToString()
	{
		return $this->getHours();
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
	 * (non-PHPdoc)
	 * @see Url/Url_Abstract#_setParts()
	 */
	protected function setParts()
	{
		$num = $this->getRegex()->getNumber();
		
		if ($this->getRegex()->hasMonths()) {
			$num = Time::convertTo($num, Time::MONTHS, Time::HOURS);
		} else if ($this->getRegex()->hasYears()) {
			$num = Time::convertTo($num, Time::YEARS, Time::HOURS);
		} else if ($this->getRegex()->hasDays()) {
			$num = Time::convertTo($num, Time::DAYS, Time::HOURS);
		}

		$this->setPart('hours', $num, false);
	}
}