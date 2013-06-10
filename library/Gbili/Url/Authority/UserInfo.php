<?php
namespace Gbili\Url\Authority;

use Gbili\Regex\Encapsulator\AbstractEncapsulator;

class UserInfo
extends AbstractEncapsulator
{
	/**
	 * 
	 * @param unknown_type $username
	 * @return unknown_type
	 */
	public function setName($username)
	{
		$this->setPartWithDirtyData('Name', $username, false);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getName()
	{
		return $this->getPart('Name');
	}
	
	/**
	 * 
	 * @param unknown_type $password
	 * @return unknown_type
	 */
	public function setPass($password)
	{
		$this->setPartWithDirtyData('Pass', $password, false);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getPass()
	{
		return $this->getPart('Pass');
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Url/Url_Abstract#_setParts()
	 */
	protected function setParts()
	{
		$this->setPart('Name', $this->getRegex()->getName(), false);
		$this->setPart('Pass', $this->getRegex()->getPass(), false);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Url/Url_Abstract#toString()
	 */
	protected function partsToString()
	{
		return (string) $this->getName() . ':' . $this->getPass() . '@';
	}
}