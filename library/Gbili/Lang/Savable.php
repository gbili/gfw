<?php
namespace Gbili\Lang;

/**
 * Lang_Savable is a placeholder for the dirtylang
 * from that dirtylang string, it will try to create
 * a Lang_ISO. If it succeeds then you can get the
 * Lang_ISO id withg $this->getISO()->getId()
 * @see Lang_ISO methods
 * @see Lang_Savable_Db_Req to see what happens when
 * there is no iso
 * 
 * @author gui
 *
 */
class Savable
extends \Gbili\Savable\Savable
{
	/**
	 * When no Lang_ISO is found
	 * and this is true, Lang_ISO_Db_Req::save()
	 * will create an id for the dirtylang value
	 * 
	 * @var boolean
	 */
	public static $saveUnnormalizedLangsForPostTreatment = true;

	/**
	 * If a Lang_ISO was
	 * matched from the dirtystring,
	 * this will be true
	 * @var boolean
	 */
	private $hasISO = false;	
	
	/**
	 * 
	 * @return void
	 */
	public function __construct($dirtyLangStr)
	{
		parent::__construct();
		$this->setElement('langDirty', (string) $dirtyLangStr);
		//try to get the lang ISO from the dirty lang str
		$langISO = new ISO($dirtyLangStr);
		if ($this->hasISO = $langISO->isValid()) {
			$this->setElement('langISO', $langISO);
		}
	}
	
	/**
	 * Holds the string 
	 * passed to the constructor
	 * 
	 * @return string
	 */
	public function getDirtyValue()
	{
		return $this->getElement('langDirty');
	}
	
	/**
	 * proxy
	 * @return string
	 */
	public function getValue()
	{
		return $this->getDirtyValue();
	}
	
	/**
	 * 
	 * @return Lang_ISO
	 */
	public function getISO()
	{
		return $this->getElement('langISO');
	}
	
	/**
	 * 
	 * @return boolean
	 */
	public function hasISO()
	{
		return $this->hasISO;
	}

}