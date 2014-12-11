<?php
namespace Gbili\Miner\Blueprint;

use Gbili\Url\Authority\Host;
use Gbili\Savable\Savable as SavableSavable;

/**
 * Miner_Persistance_Blueprint_Savable is a wrapper that helps you create and
 * save blueprints.
 * @see Miner_Persistance_Blueprint to learn what they are.
 * 
 * @author gui
 *
 */
class Savable
extends SavableSavable
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
			$host = new Host($host);
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
	
	/**
	 * 
	 * @param $path
	 * @return unknown_type
	 */
	private function validatePath($path)
	{
		$path = (string) $path;
		if (!is_dir($path)) {
			throw new Savable\Exception('The path must point to a dir');
		}
		return $path;
	}	

	
	/**
	 * When setting this, the other paths :
	 * -CallbackPath
	 * -MethodsPath
	 * will be ignored and the blueprint will
	 * look into path/to/base/dir for:
	 * /Callback/CartelmaniaCom.php
	 * 	class: Callback_CartelmaniaCom extends Miner_Persistance_Blueprint_Action_GetContents_Callback
	 * /Method/CartelmaniaCom.php
	 * 	class: Method_CaretelmaniaCom
	 * 
	 * @param unknown_type $path
	 * @return unknown_type
	 */
	public function setBasePath($path)
	{
		$path = $this->validatePath($path);
		$this->setElement('basePath', $path);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasBasePath()
	{
		return $this->isSetKey('basePath');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getBasePath()
	{
		return $this->getElement('basePath');
	}
	
	/**
	 * Path to directory where
	 * file with name authroity calemCase . php
	 * which contains the class that holds the
	 * callbacks for each action getContents
	 * that uses callback
	 * 
	 * @param $path
	 * @return unknown_type
	 */
	public function setCallbackPath($path)
	{
		$path = $this->validatePath($path);
		$this->setElement('callbackPath', $path);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasCallbackPath()
	{
		return $this->isSetKey('callbackPath');
	}

	/**
	 * 
	 * @return unknown_type
	 */
	public function getCallbackPath()
	{
		return $this->getElement('callbackPath');
	}
	
	/**
	 * Path to directory where
	 * file with name authroity calemCase . php
	 * which contains the class where the methods
	 * that actions of type Action_Extract call
	 * their methods for refactoring output
	 * 
	 * @param unknown_type $path
	 * @return unknown_type
	 */
	public function setMethodPath($path)
	{
		$path = $this->validatePath($path);
		$this->setElement('methodPath', $path);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getMethodPath()
	{
		return $this->getElement('methodPath');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasMethodPath()
	{
		return $this->isSetKey('methodPath');
	}
}
