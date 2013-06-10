<?php
namespace Gbili\Url\Authority;

use Gbili\Regex\Encapsulator\AbstractEncapsulator;

class Host
extends AbstractEncapsulator
{
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasSubdomains()
	{
		return $this->hasPart('Subdomains');
	}

	/**
	 * 
	 * @param unknown_type $subdomains
	 * @return unknown_type
	 */
	public function setSubdomains($subdomains)
	{
		$this->setPartWithDirtyData('Subdomains', $subdomains, false);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getSubdomains()
	{
		return $this->getPart('Subdomains');
	}
	
	/**
	 * 
	 * @param unknown_type $sld
	 * @return unknown_type
	 */
	public function setSLDomain($sld)
	{
		$this->setPartWithDirtyData('SLDomain', $sld, false);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getSLDomain()
	{
		return $this->getPart('SLDomain');
	}
	
	/**
	 * 
	 * @param unknown_type $tld
	 * @return unknown_type
	 */
	public function setTLDomain($tld)
	{
		$this->setPartWithDirtyData('TLDomain', $tld, false);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getTLDomain()
	{
		return $this->getPart('TLDomain');
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Url/Url_Abstract#_setParts()
	 */
	protected function setParts()
	{
		if ($this->getRegex()->hasSubdomains()) {
			$this->setPart('Subdomains', $this->getRegex()->getSubdomains(), false);
		}
		$this->setPart('SLDomain', $this->getRegex()->getSLDomain(), false);
		$this->setPart('TLDomain', $this->getRegex()->getTLDomain(), false);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	protected function partsToString()
	{
		return (string) (($this->hasSubdomains())? $this->getSubdomains() : '') . $this->getSLDomain() . '.' . $this->getTLDomain();
	}
}