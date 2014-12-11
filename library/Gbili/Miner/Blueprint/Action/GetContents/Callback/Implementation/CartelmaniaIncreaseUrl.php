<?php
//@todo this should be in user application directories. Change namespace and exception
namespace Gbili\Miner\Blueprint\Action\GetContents\Callback;

class CartelmaniaIncreaseUrl
extends AbstractCallback
{
	/**
	 * 
	 * @param unknown_type $args
	 * @return unknown_type
	 */
	protected function callback($args)
	{
		$url = new Url($args[0]);
		$path = $url->getPath();
		$regex = new Regex($path, new Regex_String_Generic('[^.\d]+(\d+)\.\w+'));
		if (!$regex->match()) {
			throw new Miner_Persistance_Blueprint_Action_GetContents_Callback_Exception('regex did not match anything');
		}
		$fimlNum = $regex->getMatches(1);
		$fimlNum = (integer) $fimlNum;
		$fimlNum++;
		$url->setPath('film' . (string) $fimlNum . '.html');
		return $url->toString();
	}
	
	/**
	 * 
	 * @param unknown_type $string
	 * @return unknown_type
	 */
	public function explode($string)
	{
		return explode(',', $string);
	}
}
