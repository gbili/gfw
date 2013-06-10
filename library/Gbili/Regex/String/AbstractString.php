<?php
namespace Gbili\Regex\String;

use Gbili\Out\Out;

/**
 * 
 * Separates the full regex into its chunks
 * <delimiter><regex><delimiter><options>
 * 
 * Each subclass must have a defaultRegex, that will
 * be used in case the user set regex is null.
 * 
 * Regex may me aproached as parts that for is provided
 * the abstract function update() that will replace the
 * very default regex with a less default one. It must be
 * able to return a regex that uses the user specified parts
 * where they are available and the default where they are not
 * ex : <part1 : default><part2 : user><part3 : default><part4 : ...>
 * depending on what is available.
 * 
 * What is the benefit of this class?
 * It validates the regex and the delimiters
 * then when subclassed it can help group the regexes
 * for later retrival
 * 
 * What this class does not do is to apply the
 * regex to an subject, it only creates a regex
 * string. that has delimiters
 * 
 * I really think this class is shit.
 * 
 * @author gui
 *
 */
abstract class AbstractString
{
	/**
	 * 
	 * @var unknown_type
	 */
	static public $escapeDelimiterCharactersOccurrenceInRegex = true;
	
	/**
	 * isRegexCompliantDelimiter will check
	 * for occurences of delimiters in the regex.
	 * When occurrences are not escaped, it will
	 * escape them automatically when allowed by
	 * static $escapeDelimiterCharactersOccurrenceInRegex
	 * when this happens, there will be a new
	 * regex stored in guessedRegex.
	 * 
	 * @var unknown_type
	 */
	static private $alertOnRegexReplace = true;
	
	/**
	 * This regex holds the regex that should be if
	 * the delimiter were to be the char passed to
	 * isRegexCompliantDelimiter in $delimiter param
	 * 
	 * @var unknown_type
	 */
	private $guessedRegex = null;
	
	/**
	 * Contains the final regex
	 * 
	 * @var unknown_type
	 */
	private $regex;
	
	/**
	 * Contains the full regex
	 * with delimiters and modifiers
	 * 
	 * @var unknown_type
	 */
	private $fullRegex;
	
	/**
	 * Contains the options that
	 * are right to the regex delimiters
	 * ex : "|asdfasdfasdfasdfasdfasd|i"
	 * -> i
	 * @var unknown_type
	 */
	private $options;
	
	/**
	 * default regex options
	 * 
	 * @var unknown_type
	 */
	private $defaultOptions = 'uis';
	
	/**
	 * The character that surrounds the regex
	 * 
	 * @var unknown_type
	 */
	private $delimiter;
	
	/**
	 * 
	 * @var unknown_type
	 */
	private $defaultDelimiter = '#';
	
	/**
	 * 
	 * @var unknown_type
	 */
	private $delimiterChars = array(1 => '#', 2 => '@', 3 => '/', 4 => '%', 5 => '{', 6 => '}', 7 => '~');
	
	/**
	 * Tells if $regex has the last version
	 * of all the final regex string components
	 * 
	 * @var unknown_type
	 */
	private $isRegexStringUpToDate = false;

	/**
	 * When the regex || options || delimiters the
	 * full regex member must be updated
	 * 
	 * @var unknown_type
	 */
	private $isFullRegexStringUpToDate = true;
	
	/**
	 * If the regex is the default regex this will be true
	 * @var unknown_type
	 */
	private $isUsingDefaultRegex;
	
	/**
	 * 
	 * @param unknown_type $regex
	 * @return unknown_type
	 */
	public function __construct($regex = null)
	{
		if (null !== $regex) {
			$this->setRegex($regex);
		}
	}

	/**
	 * Returns the default regex
	 * 
	 * @return unknown_type
	 */
	final public function getDefaultRegex()
	{
		if (!isset($this->defaultRegex)) {
			throw new Exception('Error : you must define $defaultRegex from the subclassing regex class.');
		}
		return $this->defaultRegex;
	}

	/**
	 * If regex is not set, calling this function
	 * will call setRegex with no para, which makes
	 * it pouplate with default value, and return true...
	 * So make sure to put what you want in regex
	 * before calling this.
	 * 
	 * @return unknown_type
	 */
	final public function isUsingDefaultRegex()
	{
		if (null === $this->regex) {
			$this->setRegex();
		}
		return $this->isUsingDefaultRegex;
	}
	
	/**
	 * 
	 * @param unknown_type $regex
	 * @return unknown_type
	 */
	final public function setRegex($regex = null)
	{
		//if no string is provided use the default one
		if (null === $regex) {
			$this->isUsingDefaultRegex = true;
			$regex = $this->getDefaultRegex();
		} else {
			$this->isUsingDefaultRegex = false;
		}
		if (!self::isPlausibleRegex($regex)) {
			throw new Exception('Error : setRegex Param must be a string with more than one char, given : ' . print_r($regex, true) . '.');
		}
		$this->regex = $regex;
		$this->setRegexStringAsUpToDate();
		return $this;
	}
	
	/**
	 * Call this when part of the regex must be 
	 * refreshed with a new provided value
	 * When is upToDate == false getRegex()
	 * will call update()
	 * 
	 * @return unknown_type
	 */
	final private function setRegexStringAsUpToDate()
	{
		$this->isRegexStringUpToDate = true;
	}

	/**
	 * Can be called from subclasses
	 * call this when regex sub class member is
	 * updated
	 * 
	 * @return unknown_type
	 */
	final protected function setRegexStringAsNotUpToDate()
	{
		$this->isRegexStringUpToDate = false;
		$this->setFullRegexStringAsNotUpToDate();//when regex is not up to date, then full regex neither
	}
	
	/**
	 * Allow subclass to know this
	 * @return unknown_type
	 */
	final protected function isRegexStringUpToDate()
	{
		return $this->isRegexStringUpToDate;
	}
	
	/**
	 * Call when update $fullRegex
	 * @return unknown_type
	 */
	final private function setFullRegexStringAsUpToDate()
	{
		$this->isFullRegexStringUpToDate = true;
	}
	
	/**
	 * Call this when regex || delimiter || options
	 * are updated
	 * 
	 * @return unknown_type
	 */
	final protected function setFullRegexStringAsNotUpToDate()
	{
		$this->isFullRegexStringUpToDate = false;
	}
	
	/**
	 * Returns just the regex string
	 * 
	 * @return unknown_type
	 */
	final public function getRegex()
	{
		//one subclass function estimated it is not to date
		//and called $this->setRegexStringAsNotUpToDate()
		if ($this->isRegexStringUpToDate === false) {
			$this->update();
		}
		if ($this->regex === null) {
			$this->setRegex();
		}
		return $this->regex;
	}
	
	/**
	 * Returns the regex with delimiters and options
	 * ready to be used in a preg_match function
	 * regex : asdfasdf
	 * delimiter : /
	 * fullregex : /(*UTF8)asdfasdf/
	 * @return unknown_type
	 */
	final public function getFullRegex()
	{
		if (null === $this->fullRegex || false === $this->isFullRegexStringUpToDate) {
			//the call to getDelimiter() before get regex is very important, because it may change the return value of getRegex()
			$this->fullRegex = implode(array( (string) $this->getDelimiter(), (string) $this->getRegex(), (string) $this->getDelimiter(), (string) $this->getOptions()));
		}
		return $this->fullRegex;
	}
	
	/**
	 * Sets all the regex object members (delimiter, regex, options) from the full regex
	 * 
	 * @param unknown_type $fullRegex
	 * @return unknown_type
	 */
	final public function setFullRegex($fullRegex)
	{
		if (!is_string($fullRegex)) {
			throw new Exception('Error : full regex must be a string. given : ' . print_r($fullRegex, true));
		}
		//regex should be : <delimiter><regex><delimiter><options>
		$delimiter = mb_substr($fullRegex, 0, 1);//1st <delimiter>
		$rest = mb_substr($fullRegex, 1);//rest : <regex><delimiter><options>
		
		//split the rest of the regex into two <regex> and <delimiter><options>
		$lastDelimiterPos = mb_strrpos($rest, $delimiter);
		$regex = mb_substr($rest, 0, $lastDelimiterPos);//<regex>
		$tmp = mb_substr($rest, $lastDelimiterPos);//<delimiter><options>
		
		//try to populate instance
		$this->setRegex($regex);
		//will throw an exception if not compliant
		$this->setDelimiter($delimiter);
		
		//ensure there are options (more chars than just the delimiter)
		if (1 < mb_strlen($tmp)) {
			$options = mb_substr($tmp, 1); //<options>
			$this->setOptions($options);
		}
		return $this;
	}
	
	/**
	 * Prxy
	 * @return unknown_type
	 */
	public function toString()
	{
		return $this->getFullRegex();
	}
	
	/**
	 * Uses the default delimiter if not set
	 * 
	 * @return unknown_type
	 */
	final public function getDelimiter()
	{
		if (null === $this->delimiter) {
			$this->setDelimiter($this->defaultDelimiter);
		}
		return $this->delimiter;
	}
	
	/**
	 * Sets the final regex string delimiters
	 * 1. Use a valid delimter
	 * This will test the $delimiter against the allowed set of
	 * delimiters (in array $this->delimiterChars). If it is found
	 * the $this->delimiterChars array will be reordered so the
	 * $delimiter will be the first to be checked for compliance.
	 * If the $delimiter is not in that set, it will be droped
	 * and one of the delimiters in $this->delimiterChars will be used.
	 * 
	 * 2. Check if the valid delimiter can be used with the current regex
	 * Permute the $this->delimiterChars array to find the first suitable
	 * delimiter. Remember that if the $deiliter was compliant, it will
	 * be the first in the array. Uses $this->isRegexCompliantDelimiter() to
	 * look inside regex for occurences of delimiter.
	 * 
	 * 3. Make sure a delimiter was found.
	 * If it was fond set it for the instance.
	 * Otherwise, if the user allows regex escaping to use a delimiter
	 * character that apears inside the regex, throw an exception with
	 * the escaped regex string.
	 * If he does not allow escaping, suggest him the allowed delimiters
	 * and he will change the regex|delimiter manually.
	 * ex : /
	 * 
	 * @return unknown_type
	 */
	final public function setDelimiter($delimiter)
	{
		//make sure $delimiter is the first char to be considered as delimiter
		if (false !== $k = array_search($delimiter, $this->delimiterChars)) {
			unset($this->delimiterChars[$k]);
			array_unshift($this->delimiterChars, $delimiter);
		}
		
		//premute delimiters in case the one tried is not compliant
		foreach ($this->delimiterChars as $chr) {
			if (!$this->isRegexCompliantDelimiter($chr, $this->getRegex())) {
				$foundDelimiter = false;
			} else {
				$delimiter = $chr;
				$foundDelimiter = true;
				break;
			}
		}

		if (false === $foundDelimiter) {
			$guess = (self::$escapeDelimiterCharactersOccurrenceInRegex)? ', my guess is that your regex should look something like this : ' . print_r($this->guessedRegex, true) : '';
			throw new Exception('Delimiter cannot be any of these characters: ' . $this->getRegex() . ' if it is not escaped, given : ' . $delimiter . $guess);
		}
		$this->delimiter = $delimiter;
		//update this regex if a regex has been guessed
		if (null !== $this->guessedRegex) {
			if (true === self::$alertOnRegexReplace) {
				Out::l1('Changing passed regex with guessed one, for delimiter compliancy. New regex : ' . $this->guessedRegex);
			}
			$this->regex = $this->guessedRegex;
		} 
		$this->setFullRegexStringAsNotUpToDate();//force getFullRegex to update
		return $this;
	}
	
	/**
	 * Checks if the delimiter is compliant with the given regex
	 * 
	 * @param string $delimiter
	 * @param string $regex
	 * @return boolean
	 */
	public function isRegexCompliantDelimiter($delimiter, $regex)
	{
		if (!self::isValidDelimiter($delimiter)
		 || !self::isPlausibleRegex($regex)) {
			throw new Exception('Delimiter and regex must be strings of 1 and more than zero character long strings respectively, given : ' . print_r(array($delimiter, $regex), true));
		}
		
		//make sure explode detects extreme delimiters by app|prepending a random leter that is normally not used as a delimiter
		if ('c' === $delimiter) {
			throw new Exception('The virtual delimiter used to check against delimiter is the same as the delimiter which is a problem , try to change the virtual delimiter to another character');
		}
		//app|prepend 'c' will allow to remove front and trailing occurences
		//of delimiter in regex, see implementation for understanding
		$testRegex = 'c' . $regex . 'c';
		//create chuncks that have a delimiter between them
		$dirtyChunks = explode($delimiter, $testRegex);
		$validChunks = array();
		//there are occurences of delimiter inside regex
		if (!empty($dirtyChunks) && 1 < count($dirtyChunks)) {
			//add a \ at the end of every chunck (i.e. before every occurence of delimiter, once reassembeled)
			foreach ($dirtyChunks as $part) {
				//make sure the delimiter is not already escaped by user in regex
				if ('\\' !== mb_substr($part, -1)) {
					//only escape if allowed by user
					if (!self::$escapeDelimiterCharactersOccurrenceInRegex) {
						//the delimiter is not escaped and user doesn't want it to be escaped
						return false;
					}
					//append the escape char
					$validChunks[] = $part . '\\';
				} else {
					//already escaped by user
					$validChunks[] = $part;
				}
			}
			//all delimiters have been escaped (if not already done by user)
			//put regex back together
			$this->guessedRegex = implode($delimiter, $validChunks);
			$this->guessedRegex = mb_substr($this->guessedRegex, 1, -2);
			Out::l1('The delimiter is compliant only if you change the regex to this : ' . $this->guessedRegex);
		}
		return true;
	}
	
	/**
	 * Delimiter must be a 1 character long string
	 * 
	 * @param any $delimiter
	 * @return unknown_type
	 */
	static public function isValidDelimiter($delimiter)
	{
		return ( is_string($delimiter) && (1 === mb_strlen($delimiter)) );
	}
	
	/**
	 * Regex must me a more than one character long string
	 * doesn't check if regex is well formed
	 * 
	 * @param unknown_type $string
	 * @return unknown_type
	 */
	static public function isPlausibleRegex($regex)
	{
		return ( is_string($regex) && (0 < mb_strlen($regex)) );
	}
	
	/**
	 * This will make setDelimiter echo an alert
	 * when it replaces the original regex with a
	 * guessed one.
	 * 
	 * @param $bool
	 */
	static public function alertRegexReplace($bool)
	{
		self::$alertOnRegexReplace = (boolean) $bool;
	}
	
	/**
	 * Returns the regex options:
	 * i - Case Insensitive
	 * m - Multiline mode - ^ and $ match start and end of lines
	 * s - Dotall - . class includes newline
	 * x - Extended� comments and whitespace
	 * e - preg_replace only � enables evaluation of replacement as PHP code
	 * S - Extra analysis of pattern
	 * U - Pattern is ungreedy
	 * u - Pattern is treated as UTF-8
	 * @return unknown_type
	 */
	final public function getOptions()
	{
		if (null === $this->options) {
			$this->setOptions($this->defaultOptions);
		}
		return $this->options;
	}
	
	/**
	 * Set options and validate them
	 * throw exception if invalid
	 * 
	 * @return unknown_type
	 */
	final public function setOptions($regexOptions, $keepExistingOptions = true)
	{
		if (is_string($regexOptions)) {
			$regexOptions = str_split($regexOptions);
		}
		if (!is_array($regexOptions)) {
			throw new Exception('Error : parameter must be a string or an array of one char strings.');
		}
		$actualOptions = str_split($this->options);
		foreach ($regexOptions as $option) {
			if (!self::isValidOption($option)) {
				throw new Exception('Error : option \'' . $option . '\' is not supported.');
			} else if (true === $keepExistingOptions && false ==! ($k = array_search($option, $actualOptions))) {
				unset($actualOptions[$k]);//dont have duplicate options
			}
		}
		//if want to keep existing options, add the existing options to the new ones
		if (true === $keepExistingOptions) {
			foreach ($actualOptions as $option) {
				$regexOptions[] = $option;
			}
		}
		$this->options = implode($regexOptions);
		$this->setFullRegexStringAsNotUpToDate();//force getFullRegex to update
		return $this;
	}
	
	/**
	 * Validates the option
	 * 
	 * @param unknown_type $option
	 * @return unknown_type
	 */
	static public function isValidOption($option)
	{
		switch ($option) {
			case 'i';
			break;
			case 'x';
			break;
			case 's';
			break;
			case 'S';
			break;
			case 'm';
			break;
			case 'u';
			break;
			case 'U';
			break;
			default; // if the option is none of the above return false
				return false;
			break;
		}
		return true;
	}
	
	/**
	 * Only called by getRegex() when isRegexStringUpToDate == false.
	 * isUpToDate is made false when some members of a subclass
	 * regex object have been updated via its set functions
	 * that call isUpToDate(false).
	 * As setRegex() makes isRegexStringUpToDate true, there should
	 * be no fear of overriding the regex set from setRegex()
	 * by the user as update will not be called after that.
	 * 
	 * Only implement this if there are regex components
	 * in the subclass.
	 * 
	 * @return unknown_type
	 */
	abstract protected function update();
}