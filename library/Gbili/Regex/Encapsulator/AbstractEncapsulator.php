<?php
namespace Gbili\Regex\Encapsulator;

use Gbili\Autoloader\Loader;

/**
 * Regex_String defines the regular expression including groups
 * Regex_Abstract get an input string and use the Regex_String to provide an output as groups
 * Regex_Abstract subclasses typecast which Regex_String subclass, and map the group numbers to meaningful entites with methods
 * Regex_Encapsulator allows any given class to use the functionality of the Regex "module" by
 * encapsulating the whole thing together.
 * I.E. : 
 * So it accepts the input string in the constructor
 * then generates the appropriate Regex and Regex_String subclasses and automatically tries to validate input
 * you can specify to throw exception if the input is not considered to be valid by the regex.
 * 
 * Once validation has taken place, and the regex matched something, the abstract function setParts() is 
 * called. The subclass can then set the parts individually with setPart('partName', value, storeAsObject=false)
 * It can access the regex output with $this->getRegex()->get...() and set each part. If you have
 * defined human understandable methods for accessing the matched groups in your <sub_class>_Regex
 * then you can call them like : $this->setPart('brand', $this->getRegex()->getBrand()) or call
 * $this->setPart('brand', $this->getRegex()->getGroupNumber(1))
 * The latter one has the pitfail that you may be trying to get a group number that does not exist
 * if you had defined human accessible methods you can check there if the goup exist and return a default
 * value in case it does not.
 * 
 * setPart() should only be used on a sanitized context i.e. with the validated output if you want to
 * let the user change the value of a part (from a public function) with new input (not yet validated input)
 * then you can use setPartWithDirtyData() this will force to revalidate the new input string, obtained from
 * the combination of already validated parts and the new dirty parts with the method $this->toString();
 * 
 * You must use the directory tree structure of /YourClass/Regex/String.php, /YourClass/Regex.php to make
 * the auto regex instantiation work
 * 
 * @author gui
 *
 */
abstract class AbstractEncapsulator
{
	/**
	 * 
	 * @var unknown_type
	 */
	public static $throwExceptionIfNotValid = true;
	
	/**
	 * This is needed to avoid memory allocation limit
	 * excess
	 * 
	 * @var unknown_type
	 */
	protected $skipToStringValidation = false;
	
	/**
	 * 
	 * @var string
	 */	
	private $inputString = null;
	
	/**
	 * 
	 * @var boolean
	 */
	private $isValidated = false;
	
	/**
	 * 
	 * @var Regex subclass
	 */
	protected $regex = null;
	
	/**
	 * Is also used as valid check
	 * 
	 * @var unknown_type
	 */
	private $matchedSomething = null;
	
	/**
	 * 
	 * @var unknown_type
	 */
	private $parts = array();
	
	/**
	 * Populates the class members
	 * 
	 * @param unknown_type $url
	 * @return unknown_type
	 */
	public function __construct($inputString)
	{
		$this->inputString = (string) $inputString;

		if (!$this->isValid() && !self::$throwExceptionIfNotValid == true) {
		 	throw new Exception('Error : input is not valid, input string: ' . print_r($this->inputString, true) . ' preg_match result: ' . print_r($pregRes, true));
		}
	}
	
	/**
	 * Return the dirty url string 
	 * passed as constructor param
	 * 
	 * @return unknown_type
	 */
	public function getString()
	{
		return $this->inputString;
	}
	
	/**
	 * Tells itf the url string apears to be compliant with its
	 * reciproque regex under /Miner/Regex/Url/... 
	 * 
	 * @param $urlString
	 * @return unknown_type
	 */
	public function isValid()
	{
		//return if allready checked
		if (true === $this->isValidated && null !== $this->matchedSomething) {
			return $this->matchedSomething;
		}
		//allow to validate from the peaces set from setter (like setPath()) or if not available from $this->urlString
		if (!empty($this->parts)) {
			//avoid memory allocation limit excess
			$this->skipToStringValidation = true;
			$this->getRegex()->setInputString($this->toString());
			$this->skipToStringValidation = false;
		}
		if ($this->matchedSomething = $this->getRegex()->match()) {
			//call subclass function to set each part from the regex object matches
			$this->setParts();//modifies $this->matchedSomething to false when one part is not valid (does not match anything)
		} else if (!$this->getRegex()->isValid()) {
			throw new Exception('The regex return by class : ' . get_class($this->getRegex()) . ' is not valid.');
		}
		$this->isValidated = true;
		//set parts will alter the is valid value so make sure we dont overwrite it
		return $this->matchedSomething;
	}
	
	/**
	 * If any of the parts is not valid
	 * then the whole container is not valid
	 * recursive...
	 * 
	 * @param string $name
	 * @param string $value
	 * @return unknown_type
	 */
	protected function setPart($name, $value, $storeAsObject = true)
	{
		if (true === $storeAsObject) {
			$classname = $this->getClassname(ucfirst(mb_strtolower($name))); //@todo what about $name = HelloDolly-> Hellodolly? bad
			if (is_string($value)) {
				$value = new $classname($value);
			}
			if (!($value instanceof $classname)) {
				throw new Exception('The value must be an instance of : ' . $classname . ' when $storeAsObject is true');
			}
			if (!$value->isValid()) { //only alter when not valid
				$this->matchedSomething = false;
			}
		} else if (is_object($value)) {
			throw new Exception('The value is suposed to be a string not an object, given : ' . print_r($value, true) . 'set the value to a string or change the 3 param to true');
		}
		$this->parts[(string) $name] = $value;
	}
	
	/**
	 * Same as set part but use this when the data
	 * comes from user input as in a public setElement()
	 * 
	 * @param unknown_type $name
	 * @param unknown_type $value
	 * @param unknown_type $storeAsObject
	 * @return unknown_type
	 */
	protected function setPartWithDirtyData($name, $value, $storeAsObject = true)
	{
		$this->isValidated = false;
		$this->setPart($name, $value, $storeAsObject);
	}
	
	/**
	 * 
	 * @param unknown_type $name
	 * @return unknown_type
	 */
	protected function hasPart($name)
	{
		return isset($this->parts[$name]);
	}
	
	/**
	 * 
	 * @param unknown_type $name
	 * @return unknown_type
	 */
	protected function getPart($name)
	{
		if (!isset($this->parts[$name])) {
			throw new Exception('Trying to get part with name : ' . (string) $name . ' in : ' . get_class($this) . ' and it is not set');
		}
		return $this->parts[$name];
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	protected function getParts()
	{
		return $this->parts;
	}
	
	/**
	 * If the regex object matches something, then this
	 * method is called so the subclass can set its
	 * parts from the regex results and name each
	 * part as it wants.
	 * 
	 * @return unknown_type
	 */
	abstract protected function setParts();
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getRegex()
	{
		if (null === $this->regex) {
			$regexClassname = $this->getClassname('Regex');
			$regexStrClassname = $this->getClassname('Regex\String');
			$this->regex = new $regexClassname($this->inputString, new $regexStrClassname()); 
		}
		return $this->regex;
	}
	
	/**
	 * Two places are allowed 
	 * 1. get_class($this)\Regex[\String]
	 * 2. get_class($this)\..\Regex[\String]
	 * 
	 * In 1, My\Url would map to My\Url\Regex[\String]
	 * In 2, My\Url\Url would be remaped to My\Url\Regex[\String]
	 * 
	 * Some frameworks have to push their root classes to some
	 * folder named after them, to make the class modular, that
	 * is how we end up with classes with repeated names like Url\Url
	 * 
	 * If \Regex is found in one place,\Regex\String has to be
	 * in the same type of place
	 * 
	 * Try to find file of classname, if not found, return second
	 * type, it will throw up down the road.
	 * 
	 */
	public function getClassname($forClassPart)
	{
	    $baseClassname = '\\' . get_class($this);
        if (!class_exists($baseClassname . '\\' . $forClassPart)) {
        //if (!Loader::getLoader()->findFile($baseClassname . '\\' . $forClassPart)) {
            $baseClassname = substr($baseClassname, 0, strrpos($baseClassname, '\\'));
        }
        return  $baseClassname . '\\' . $forClassPart;
   	}
	
	/**
	 * 
	 * @param Regex_Abstract $regex
	 * @return unknown_type
	 */
	public function setRegex($regex)
	{
		$classname = $this->getClassname('Regex');
		if (!($regex instanceof $classname)) {
			throw new Exception('The regex instance must be of type : ' . $classname);
		}
		$this->isValidated = false;
		$this->regex = $regex;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function isValidated()
	{
		return (true === $this->isValidated);
	}
	
	/**
	 * Retruns a normalized url
	 * 
	 * @return unknown_type
	 */
	final public function toString()
	{
		if (!$this->skipToStringValidation && !$this->isValid()) {
			throw new Exception('Cannot call toString() if Url_Regex did not match anything');
		}
		return $this->partsToString();
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	abstract protected function partsToString();
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function toArray($oneLevel = false)
	{
		$array = array();
		if (!empty($this->parts)) {
			foreach ($this->parts as $name => $part) {
				if ($part instanceof self) {
					$part = $part->toArray($oneLevel);	
				}
				if (true === $oneLevel && is_array($part)) {
					foreach ($part as $subName => $subart) {
						$array[$subName] = $subart;
					}
				} else {
					$array[$name] = $part;
				}
			}
		}
		return $array;
	}
}
