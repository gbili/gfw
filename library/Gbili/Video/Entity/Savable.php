<?php
namespace Gbili\Video\Entity;

use Gbili\Lang\Savable   as LangSavable,
    Gbili\Source\Savable as SourceSavable;

/**
 * Contains data that identifies a
 * video. Let's say all metadata
 * 
 * @todo On lang : if lang is not normalized and $saveUnnormalizedLangsForPostTreatment
 * is true then save the unormalized lang to Db and associate it to the video entity
 * ensure the unormalized lang is not repeated in Db by doing a select before saving
 * then, all unormalized langs can be normalized manually...
 * 
 * @author gui
 *
 */
class Savable
extends \Gbili\Savable\Savable
{

	/**
	 * This is the language that the dumped site is in
	 * this is useful to give a language to all synopsis
	 * It allows video entities to have synopsis
	 * in many different languages. When dumping from
	 * different sites
	 * 
	 * @var 
	 */
	private static $dumpedSiteDefaultLang;
	
	/**
	 * If no lang is found during the dumping process,
	 * this lang will be used instead
	 * 
	 * @param $l
	 * @return void
	 */
	public static function setDumpedSiteDefaultLang(LangSavable $l)
	{
		if (false === $l->hasISO()) {
			throw new Exception('the LangSavable passed as dumpedSiteDefaultLang must have an iso, given : ' . $l->getValue());
		}
		self::$dumpedSiteDefaultLang = $l;
	}
	
	/**
	 * @return LangSavable
	 */
	public static function getDumpedSiteDefaultLang()
	{
		if (null === self::$dumpedSiteDefaultLang) {
			throw new Exception('the dumpedSiteDefaultLang must be set'); 
		}
		return self::$dumpedSiteDefaultLang;
	}
	
	/**
	 * 
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * 
	 * @param unknown_type $title
	 * @return void
	 */
	public function setTitle(Title\Savable $title)
	{
		$this->setElement('title', $title);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasTitle()
	{
		return $this->isSetKey('title');
	}
	
	/**
	 * 
	 * @param unknown_type $title
	 * @return unknown_type
	 */
	public function getTitle()
	{
		return $this->getElement('title');
	}
	
	/**
	 * Even if not valid the lang is stored here, and then the requestor
	 * will need to decide whether to save de langDirty or not depending
	 * on International_LangISO::$saveUnnormalizedLangsForPostTreatment
	 * 
	 * @param International_LangISO $lang
	 * @return unknown_type
	 */
	public function setLang(LangSavable $lang)
	{
		$this->setElement('lang', $lang);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getLang()
	{
		if ( ! $this->isSetKey('lang')) {
			//use default lang when it was not set in dumping process
			$this->setLang(self::getDumpedSiteDefaultLang());
		}
		return $this->getElement('lang');
	}
	
	/**
	 * 
	 * @param unknown_type $d
	 * @return unknown_type
	 */
	public function setSynopsis($d)
	{
		$this->setElement('synopsis', (string) $d);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getSynopsis()
	{
		return $this->getElement('synopsis');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasSynopsis()
	{
		return $this->isSetKey('synopsis');
	}

	/**
	 * 
	 * @return unknown_type
	 */
	public function hasSharedInfo()
	{
		return $this->isSetKey('sharedInfo');
	}

	/**
	 * Automatically creates the shared info object
	 * if not already set
	 * 
	 * @return unknown_type
	 */
	public function getSharedInfo()
	{
		if (false === $this->isSetKey('sharedInfo')) {
			$this->setSharedInfo(new SharedInfo\Savable());
		}
		return $this->getElement('sharedInfo');
	}

	/**
	 * 
	 * @param Video_Entity_SharedInfo $shI
	 * @return unknown_type
	 */
	public function setSharedInfo(SharedInfo\Savable $shI)
	{
		$this->setElement('sharedInfo', $shI);
	}

	/**
	 * Overwrites existing sources
	 * 
	 * @param array $sources
	 * @return unknown_type
	 */
	public function setSources(array $array)
	{
		array_map(function ($source) {
                      if (!($source instanceof SourceSavable))
                          throw new Exception('You are trying to add at least a source which is not one');
                  },
		          $array);
		//don't overwrite the existing sources origin, just put them as outdatedAtom
		//and add this ones to the current and use this origin
		$this->useKeyAsArrayAndPushValue('sources', $array, false, false);
	}

	/**
	 * 
	 * @param Source $source
	 * @return unknown_type
	 */
	public function addSource(SourceSavable $source)
	{
		$this->useKeyAsArrayAndPushValue('sources', $source, \Gbili\Savable\Savable::POST_SAVE_LOOP);
	}

	/**
	 * 
	 * @param array $array
	 * @return unknown_type
	 */
	public function addSources(array $array)
	{
		foreach ($array as $source) {
			if (!($source instanceof SourceSavable)) {
				throw new Exception('You are trying to add at least a source which is not one');
			}
		}
		//overwrite existing sources array origin, with the one at the time
		//of this function call, and push the sources to the existing array
		$this->useKeyAsArrayAndPushValue('sources', $source, \Gbili\Savable\Savable::POST_SAVE_LOOP);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getSources()
	{
		return $this->getElement('sources');
	}
}