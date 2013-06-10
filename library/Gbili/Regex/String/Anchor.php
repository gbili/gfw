<?php
namespace Gbili\Regex\String;

class Anchor 
extends AbstractString
{
	/**
	 * Contains the very default regex
	 * 
	 * @var unknown_type
	 */
	protected $defaultRegex = '<a\s[^>]*href=("??)([^" >]*?)\\1[^>]*>(.*?)<\/a>';
	
	/**
	 * 
	 * @var unknown_type
	 */
	private $urlRegex;
	
	/**
	 * 
	 * @var unknown_type
	 */
	private $defaultUrlRegex = '[^" >]*?';
	
	/**
	 * 
	 * @var unknown_type
	 */
	private $textRegex;
	
	/**
	 * 
	 * @var unknown_type
	 */
	private $defaultTextRegex = '.*?';
	
	/**
	 * 
	 * @param string $urlRegex
	 * @param string $textRegex
	 * @return null
	 */
	public function __construct($urlRegex = null, $textRegex = null)
	{
		if (null !== $urlRegex) {
			$this->setUrlRegex($urlRegex);
		} 
		if (null !== $textRegex) {
			$this->setTextRegex($textRegex);
		}
	}

	/**
	 * If no argument set url to accept everything
	 * 
	 * @return unknown_type
	 */
	public function setUrlRegex($urlRegex)
	{
		if ($urlRegex instanceof \Gbili\Url\Regex\String) {
			$urlRegex = $urlRegex->getRegex();
		}
		if (!is_string($urlRegex)) {
			throw new Exception('Error : The parameter must be a string or a \\Gbili\\Url\\Regex\\String Instance, given : ' . print_r($urlRegex, true));
		}
		$this->urlRegex = $urlRegex;
		//we want getRegex() to include the new url
		//when it calls update()
		$this->setAsNotUpToDate();
		return $this;
	}

	/**
	 * 
	 * @return unknown_type
	 */
	public function getUrlRegex()
	{
		if (null === $this->urlRegex) {
			$this->setUrlRegex($this->defaultUrlRegex);
		}
		return $this->urlRegex;
	}

	/**
	 * 
	 * @param unknown_type $textRegex
	 * @return unknown_type
	 */
	public function setTextRegex($textRegex)
	{
		if (!is_string($textRegex)) {
			throw new Exception('Error : The parameter must be a string given : ' . print_r($textRegex, true));
		}
		$this->textRegex = $textRegex;
		$this->setAsNotUpToDate();
		return $this;
	}

	/**
	 * 
	 * @return unknown_type
	 */
	public function getTextRegex()
	{
		if (null === $this->textRegex) {
			$this->setTextRegex($this->defaultTextRegex);
		}
		return $this->textRegex;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	private function update()
	{
		return $this->setRegex("<a\s[^>]*href=(\"??)({$this->getUrlRegex()})\\1[^>]*>({$this->getTextRegex()})<\/a>");
	}
}