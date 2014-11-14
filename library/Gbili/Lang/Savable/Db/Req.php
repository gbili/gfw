<?php
namespace Gbili\Lang\Savable\Db;

use Gbili\Db\Req\AbstractReq;

/**
 * 
 * @author gui
 *
 */
class Req
extends AbstractReq
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
	 * Only save if the lang dirty does not exist
	 * 
	 * @return unknown_type
	 */
	public function save(Lang_Savable $lang)
	{
		if (false === $lang->hasISO() 
		 && true === Lang_Savable::$saveUnnormalizedLangsForPostTreatment) {
			if (false === ($idOrFalse = $this->existsLangDirty($lang->getValue(), true))) {
				$this->insertUpdateData("INSERT INTO LangDirty (value) VALUES (:langDirty)",
										array(':langDirty' => (string) $lang->getValue()));
				$idOrFalse = $this->getAdapter()->lastInsertId();
			}
			$lang->setId($idOrFalse);
		} else {
			$lang->setId($lang->getISO()->getId());//there must be an id for each savable savable... so set the iso
		}
	}
	
	/**
	 * 
	 * @param unknown_type $lang
	 * @return boolean | integer
	 */
	public function existsLangDirty($lang, $returnIdIfAvailable = false)
	{
		return $this->existsElement("SELECT ld.langDirtyId AS id FROM LangDirty AS ld WHERE ld.value = :langDirty", 
									array(':langDirty' => (string) $lang),
									'id',
									(boolean) $returnIdIfAvailable);
	}
	
	public function delete(\Gbili\MIE\Savable $mIE)
	{
		
	}
}