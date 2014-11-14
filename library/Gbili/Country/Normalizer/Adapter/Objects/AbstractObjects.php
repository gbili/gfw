<?php
namespace Gbili\Country\Normalizer\Adapter\Objects;

use Gbili\Country\Normalizer\Adapter\Exception;

/**
 * These classes are meant to normalize a country
 * name. España -> Spain
 * Schweiz -> Switzerland ...
 * A set of classes, one per supported country will
 * recive a string and return the normalized version
 * if they know how to do it, otherwise they return
 * false.
 * Supported langs : 
 * ES,FR,PT,DE,IT,EN, (these langs correspond to lang
 * in which the countryStr may be passed to be recognized)
 * @author gui
 *
 */
abstract class AbstractObjects
{
	/**
	 * contains the regex to match
	 * the country str
	 * 
	 * to be overriden
	 * @var unknown_type
	 */
	protected $regex;
	/**
	 * contains the lang spoken
	 * in the country, if country
	 * speaks many use array.
	 * Use the langs from
	 * International_LangISO
	 * constants
	 * 
	 * 
	 * to be overriden
	 * @var unknown_type
	 */
	protected $langISO;
	
	/**
	 * Tells if the country
	 * speaks many langs
	 * 
	 * @var unknown_type
	 */
	private $multilang;
	
	/**
	 * Boostrap and force sbclasses
	 * to specify $regex and $langISO
	 * 
	 * @return unknown_type
	 */
	final public function __construct()
	{
		if (null === $this->regex) {
			throw new Exception('You must specify the $regex member string from the subclass.');
		}
		if (null === $this->langISO) {
			throw new Exception('You must specify the $langISO member string from the subclass.');
		}
		//if lang iso is an array then multilang is true
		$this->multilang = is_array($this->langISO);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	final public function getRegex()
	{
		return $this->regex;
	}
	
	/**
	 * Corresponds to the lang spoken
	 * in the country
	 *  
	 * @return unknown_type
	 */
	final public function getLangISO()
	{
		return $this->langISO;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	final public function isMultilang()
	{
		return $this->multilang;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	final public function getCountryName()
	{
		$str = get_class($this); //Bla_Bla_France
		return end(explode('\\', $str));//array(Dupmer,Bla,Bla,France) -> France
	}
}