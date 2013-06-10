<?php
namespace Gbili\Lang;

/**
 * This class is used By Lang_Savable to validate
 * the input. You can pass a dirty lang and it will
 * get the iso equivalent when possible (the lang passed is supported)
 * To validate the lang it uses the normalizer which uses one of
 * the adapters available, ie the storage adapter which has been
 * implemented.
 * Every lang iso has an id
 * 
 * @author gui
 *
 */
class ISO
{	
	/**
	 * 
	 * @var unknown_type
	 */
	private $id = null;
	
	/**
	 * The lang iso
	 * @var unknown_type
	 */
	private $langISO;
	
	/**
	 * Contains the lang before normalization
	 * 
	 * @var unknown_type
	 */
	private $dirtyLangStr;
	
	/**
	 * 
	 * @param unknown_type $langIso
	 * @return unknown_type
	 */
	public function __construct($dirtyLangStr)
	{
		$this->dirtyLangStr = (string) $dirtyLangStr;
		$normalizer = ISO\Normalizer::getInstance();
		if (is_numeric($id = $normalizer->isNormalizedLangISOStr($dirtyLangStr))) {
			$this->id = $id;
			$this->langISO = $dirtyLangStr;
			$this->isValid = true;
		} else {
			$langISO = $normalizer->guessNormalizedInfo($this->dirtyLangStr);
			if ($this->isValid = (boolean) $langISO) {
				$this->langISO = $langISO;
				if ($normalizer->hasLangISOId()) {
					$this->id = $normalizer->getLangISOId();
				}
			}
		}
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getDirtyLang()
	{
		return $this->dirtyLangStr;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getValue()
	{
		if (false === $this->isValid) {
			throw new Exception('The lang could not be validated');
		}
		return $this->langISO;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasId()
	{
		return (null !== $this->id);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getId()
	{
		if (null === $this->id) {
			throw new Exception('The normalizer did not provide any id');
		}
		return $this->id;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function isValid()
	{
		return $this->isValid;
	}
}