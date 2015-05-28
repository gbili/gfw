<?php
namespace Gbili\Miner\Blueprint;

/**
 * Miner_Persistance_Blueprint_Savable is a wrapper that helps you create and
 * save blueprints.
 * @see Miner_Persistance_Blueprint to learn what they are.
 * 
 * @author gui
 *
 */
class Savable
extends \Gbili\Savable\Savable
{
	/**
	 * 
	 * @return unknown_type
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * 
	 * @param unknown_type $host
	 * @return unknown_type
	 */
	public function setHost($host)
	{
		if (is_string($host)) {
			$host = new \Gbili\Url\Authority\Host($host);
		}
		$this->setElement('host', $host);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getHost()
	{
		return $this->getElement('host');
	}
	
	/**
	 * 
	 * @param Miner_Persistance_Blueprint_Savable $action
	 * @return unknown_type
	 */
	public function setNewInstanceGeneratingPointAction(Action\Savable\AbstractSavable $action)
	{
		if ($this->isSetKey('newInstanceGeneratingPointAction')) {
			throw new Savable\Exception('The new instance generating point action is already set');
		}
		$this->setElement('newInstanceGeneratingPointAction', $action, parent::POST_SAVE_LOOP);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasNewInstanceGeneratingPointAction()
	{
		return $this->isSetKey('newInstanceGeneratingPointAction');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getNewInstanceGeneratingPointAction()
	{
		return $this->getElement('newInstanceGeneratingPointAction');
	}
}
