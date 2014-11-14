<?php
namespace Gbili\Country\Normalizer\Adapter;

use Gbili\Lang\ISO;
/**
 * This class is meant to be used for Normalization
 * it extends Abstract and gets the data from an
 * array
 * 
 * @author gui
 *
 */
class Arrays
extends AbstraceAdapter
{
	/**
	 * 
	 * 
	 * @var unknown_type
	 */
	private $countryMatchersArray = array(
					'Spain' => 
						array('regex'   => '/Espa\\p{L}\\p{M}*[ae]|Spa(?:nien|gna)|Spain/i',
							  'langISO' => array(ISO::ES,
												ISO::CA,
												ISO::GL,
												ISO::EU)),
					'Australia' => 
						array('regex'   => '/Austr\\p{L}\\p{M}*li(?:a|en)/i',
							  'langISO' => array(ISO::EN)),
					'Portugal' => 
						array('regex'   => '/Portugal|Portogallo/i',
							  'langISO' => array(ISO::PT)),
					'France' => 
						array('regex'   => '/Fran\\p{L}\\p{M}*(?:ia|[ea]|kreich)/i',
							  'langISO' => array(ISO::FR)),
					'Usa' => 
						array('regex'   => '/u\\.?s\\.?a\\.?|e\\.?e\\.?u\\.?u\\.?|Vereinigte[- _.]Staa?ten|Estados[- _.]Un\\p{L}\\p{M}*dos.*?(?:Am\\p{L}\\p{M}*rica)?|United[- _.]states.*?(?:America)?|\\p{L}\\p{M}*tats[-_. ]unis[-_. ]d?.?Am\\p{L}\\p{M}*rique|Stati[ -_.]Uniti(?:[ -_.]d.america)?/i',
							  'langISO' => array(ISO::EN)),
					'Canada' => 
						array('regex'   => '/[CK]anad\\p{L}\\p{M}*/i',
							  'langISO' => array(ISO::EN,
												ISO::FR)),
					'Austria' => 
						array('regex'   => '/\\p{L}\\p{M}*e?sterreich|Au(?:stria|triche)/i',
							  'langISO' => array(ISO::DE)),
					'Switzerland' => 
						array('regex'   => '/Switzerland|Suisse|Suiza|Svizzera|Schweiz|Su\\p{L}\\p{M}*\\p{L}\\p{M}*a/i',
							  'langISO' => array(ISO::FR,
												ISO::DE,
												ISO::IT)),
					'Belgium' => 
						array('regex'   => '/B\\p{L}\\p{M}*lgi(?:um|que|ca|en)/i',
							  'langISO' => array(ISO::FR)),
					'Uk' => 
						array('regex'   => '/[UV]\\.?K\\.?|United Kingdom|Britain|Royaume-Uni|Re[ig]no Uni[dt]o|Vereinigtes K(?:\\p{L}\\p{M}*|oe)nigreich/i',
							  'langISO' => array(ISO::EN)),
					'Italy' => 
						array('regex'   => '/(?:It\\p{L}\\p{M}*l[yi](?:a|(?:en))?)/i',
							  'langISO' => array(ISO::IT)),
					'Germany' => 
						array('regex'   => '/German(?:y|ia)|Aleman[ih]a|Allemagne|Deutschland/i',
							  'langISO' => array(ISO::DE)),
					'Argentina' => 
						array('regex'   => '/Argentin(?:[ae]|ien)/i',
							  'langISO' => array(ISO::ES)),
					'Netherlands' => 
						array('regex'   => '/Pays-Bas|Pa\\p{L}\\p{M}*ses-Ba(?:j|ix)os|Paesi-Bassi|Niederlande|Netherlands/i',
							  'langISO' => array(ISO::NL))
					);
	

	/**
	 * (non-PHPdoc)
	 * @see International/Country/Normalizer/Adapter/Country_Normalizer_Adapter_Abstract#getNext()
	 */
	public function getNext()
	{
		
		if (!(list($cName, $array) = each($this->countryMatchersArray))) {
			return false;//end of array
		}
		return array($cName => $array);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see International/Country/Normalizer/Adapter/Country_Normalizer_Adapter_Abstract#reset()
	 */
	public function reset()
	{
		reset($this->countryMatchersArray);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see International/Country/Normalizer/Adapter/Country_Normalizer_Adapter_Abstract#getCountries()
	 */
	public function getCountries()
	{
		return array_keys($this->countryMatchersArray);
	}

	/**
	 * (non-PHPdoc)
	 * @see International/Country/Normalizer/Adapter/Country_Normalizer_Adapter_Abstract#isNormalizedCountryStr($dirtyCountryStr)
	 */
	public function isNormalizedCountryStr($dirtyCountryStr)
	{
		return array_key_exists($dirtyCountryStr, $this->countryMatchersArray);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see International/Country/Normalizer/Adapter/Country_Normalizer_Adapter_Abstract#getLangISOFromNormalizedCountry($normalizedCountryStr)
	 */
	public function getLangISOFromNormalizedCountry($normalizedCountryStr)
	{
		if (!$this->isNormalizedCountryStr($normalizedCountryStr)) {
			return false;
		}
		return $this->countryMatchersArray[$normalizedCountryStr]['langISO'];
	}

}