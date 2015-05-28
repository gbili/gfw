<?php
namespace Gbili\Regex;

use Gbili\Regex\String\AbstractString;

/**
 * This class wraps the php preg_match and preg_match_all functions
 * It adds some commodities such as avoiding to
 * pass a $matches variable.
 * Plus it helps in retrieving the preg_match_all elements in an ordered
 * fashion with the function getNextMatch(). It remembers all the matches
 * in a preg_match_all and is able to return one match element at a time.
 * You can use getMatches() to get the whole set of matches of a preg_match_all 
 * or a preg_match. If you specify an index
 * 
 * @author gui
 *
 */
class AbstractRegex
{
	/**
	 * Tells whether preg match found something
	 * or if there was an error
	 * 
	 * @var boolean
	 */
	private $isValid;
	
	/**
	 * Tells whether the isValid member is
	 * syncronized with the input and regex str.
	 * Only true if it has already been validated
	 * (i.e. called match or matchAll)
	 * and the user wont edit regex str obj
	 * 
	 * @var unknown_type
	 */
	private $isValidated;
	
	/**
	 * There are the two regex functions:
	 * preg_match and preg_match_all. As
	 * the results are retrieved from the
	 * same function, this will help the
	 * function in determining how to return
	 * a result.
	 * 
	 * @var unknown_type
	 */
	private $isMatchAll;
	
	/**
	 * The string that needs to be validated
	 * 
	 * @var unknown_type
	 */
	private $inputString;
	
	/**
	 * A subclass of AbstractString
	 * 
	 * @var AbstractString
	 */
	private $regexStringObject;
	
	/**
	 * all matches of preg_match_all
	 * 
	 * @var array
	 */
	private $matches;
	
	/**
	 * Matches of a preg_match call
	 * or the match being treated by getNextMatch in a preg_match_all
	 * 
	 * @var unknown_type
	 */
	private $currentMatch;
	
	/**
	 * Contains all the elements that where shifted
	 * when calling getNextMatch (only if using matchAll())
	 * @var unknown_type
	 */
	private $matchesShifted;
	
	/**
	 * true if regex is valid and matches or current match as something in it
	 * @var number|false
	 */
	private $matchesCount;
	
	/**
	 * 
	 * @param unknown_type $input
	 * @param AbstractString $regexStringObject
	 * @return unknown_type
	 */
	public function __construct($input, AbstractString $regexStringObject)
	{
		$this->inputString = (string) $input;
		$this->regexStringObject = $regexStringObject;
		$this->isValidated = false;
		$this->isMatchAll = null;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getRegexStringObject($iWillEditRegexStringObject = true)
	{
		$this->isValidated = (!$iWillEditRegexStringObject && $this->isValidated);
		return $this->regexStringObject;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function setRegexStringObject(AbstractString $regexStringObject)
	{
		$this->regexStringObject = $regexStringObject;
		$this->isValidated = false;
		return $this;
	}
	
	/**
	 * The string against which the regex pattern (in regexStringObject) will be applied
	 * @return unknown_type
	 */
	public function getInputString()
	{
		return $this->inputString;
	}
	
	/**
	 * The string against which the regex pattern will be applied
	 * @return unknown_type
	 */
	public function setInputString($input)
	{
		if (!is_string($input)) {
			throw new Exception('Error : The first param must be a string given : ' . print_r($input, true));
		}
		$this->inputString = $input;
		$this->isValidated = false;
		return $this;
	}
	
	/**
	 * This will call match()
	 * 
	 * @return unknown_type
	 */
	public function isValid()
	{
		if (false === $this->isValidated) {
			throw new Exception('You must call match() or matchAll() before isValid()');
		}
		return $this->isValid;
	}
	
	/**
	 * Run a preg_match function to the regex
	 * and keep the results.
	 * Use getMatches() to retrieve them
	 * 
	 * Overwrites a matchAll() call
	 * 
	 * @return boolean (tells whether there is a match or not)
	 */
	public function match()
	{
		//if has not been validated validate (or the function called was matchAll do it again)
		if ($this->isValidated === false || $this->isMatchAll === true) {
			$this->updateStatus(preg_match($this->getRegexStringObject()->getFullRegex(), $this->getInputString(), $this->currentMatch), false);
		}
		return (bool) $this->matchesCount;
	}
	
	/**
	 * Run a preg_match_all function to the regex
	 * and keep the results
	 * Use getMatches() to retrieve them
	 * and goToNextMatch() to point to the next match
	 * 
	 * Overwrites a match() call
	 * 
	 * @return boolean (tells whether there are matches or not)
	 */
	public function matchAll()
	{
		//if has not been validated validate (or the function called was a simple match() validate it again)
		//or stated reversly : if it was validated with a match all, don't do it again
		if ($this->isValidated === false || $this->isMatchAll === false ) {
			$this->updateStatus(preg_match_all($this->getRegexStringObject()->getFullRegex(), $this->getInputString(), $this->matches, PREG_SET_ORDER), true);
			//make sure pointer is on first element
			reset($this->matches);
			//fill current match
			if ((bool) $this->matchesCount) {
			    $this->goToNextMatch();
			}
		}
		return (bool) $this->matchesCount;
	}

    /**
     *
     */
    public function execute($matchAll=false)
    {
        return ($matchAll)
            ? $this->matchAll()
            : $this->match();
    }
	
	/**
	 * 
	 * @param unknown_type $res
	 * @param unknown_type $matchAll
	 */
	protected function updateStatus($res, $isMatchAll = false)
	{
		$this->isValid          = (false !== $res);//if res = 0 or 1 the regex is valid
		$this->matchesCount     = $res;
		$this->isValidated      = true;
		$this->isMatchAll       = $isMatchAll;
	}
	
	/**
	 * Only call this if you used the matchAll
	 * function or if you did not validate. Otherwise it
	 * will throw up.
	 * 
	 * This function will shift the matches array and put
	 * the value (which is a one level array of matching group => value like in a preg_match function) in $this->currentMatch.
	 * It will also keep the shifted values in matchesShifted so all matches can be retrieved any time with
	 * an addition : $this->matchesShifted + $this->matches
	 * 
	 * @return boolean
	 */
	public function goToNextMatch()
	{
		$this->validateAndThrowIfNotValid();

		if ($this->isMatchAll === false) {
			throw new Exception('You cannot call, Regex::goToNextMatch() if you called Regex::match() previously instead of matchAll()');
		}

		if (false === (bool) $this->matchesCount) {
			throw new Exception('there are no matches');
		}

		//tell the caller if there are more matches
		return (boolean) list(, $this->currentMatch) = each($this->matches);
		
	}
	
    /**
     *
     * @return number|false
     */	
	public function getMatchesCount()
	{
	    return $this->matchesCount;
	}
	
	/**
	 * 
	 * @return boolean
	 */
    public function hasMoreMatches()
    {
        $currentKey = key($this->matches);
        return $currentKey < (count($this->matches)-1);
    }	

	/**
	 * It will return all the matches from a preg_match or preg_match_all
	 * or if parameter is given, it will return the value of the specified group
	 * in $group (for the current match, when isMatcAll true)
	 * Use getCurrent()
	 * 
	 * @param integer $group
	 * @param boolean $getAllMatches if true when match all, it will return matches, otherwise returns currentMatch
	 * @return array | string | null
	 */
	public function getMatches($group = null, $getAllMatches = true)
	{
		$this->validateAndThrowIfNotValid();
		//return value of key in matches
		if (null !== $group) {
			if (false === $this->hasGroup($group)) {
				throw new Exception("You are trying to get a group that does not exist. Group : $group, matches : {$this->currentMatch}");
			}
			return $this->currentMatch[$group];
		}
		//preg_match_all
		if (true === $this->isMatchAll
		 && true === $getAllMatches) {
			return $this->matches;
		}
		return $this->currentMatch;
	}
	
	/**
	 * Proxy
	 * @return unknown_type
	 */
	public function getCurrentMatch()
	{
		return $this->getMatches(null, false);
	}
	
	/**
	 * 
	 * @return multitype:
	 */
	public function getCurrentNamedGroupsMatches()
	{
	    $matches                 = $this->getCurrentMatch();
        $count                   = count($matches);
        $numericallyIndexedArray = range(0, $count-1);
        return array_diff_key($matches, $numericallyIndexedArray);
	}
	
	/**
	 * 
	 * @return multitype:
	 */
	public function getCurrentNumericGroupsMatches()
	{
	    return array_diff_key($this->getCurrentMatch(), $this->getCurrentNamedGroupsMatches());
	}
	
	/**
	 * Tells whether the group wth number $group exists in currentMatch
	 * 
	 * @param integer $group
	 * @return unknown_type
	 * @todo replace all occurences of hasGroupNumber with hasGroup
	 */
	public function hasGroup($group) 
	{    
		$this->validateAndThrowIfNotValid();
		return (isset($this->currentMatch[$group]) && '' !== $this->currentMatch[$group]);
	}
	
	/**
	 * 
	 * @param unknown_type $part
	 * @return unknown_type
	 */
	protected function validateAndThrowIfNotValid()
	{   
		if (false === $this->isValidated 
            && false === ((true === $this->isMatchAll)? $this->matchAll() : $this->match())
		) {
			throw new Exception('The regex didn\'t match anything : ' . print_r($this, true));
		}
		
		if (!$this->isValid) {
		    $callers = debug_backtrace();
			$msg = 'The input string is not valid cannot call ' . $callers[1]['function'] . '()';
			throw new Exception($msg);
		}
	}
}
