<?php
namespace Gbili\Savable;

/**
 * This class is used instead of reflection..
 * It is intended to avoid lots of code repetition like
 * if !isset($this->property) throw ... but you know...
 * life is difficult and we never know what is good or bad
 * 
 * @author gui
 *
 */
abstract class AbstractSavable
{
	const SOURCE_USEKEYASARRAYANDPUSHVALUE = 21;
	const SOURCE_SETELEMENT = 22;
	
	/**
	 * Contains all elements as an array of atoms
	 * wich are sort of wrappers that ease scope
	 * resolution
	 * 
	 * @var array
	 */
	private $elements = array();
	
	/**
	 * This is the set of keys in $elements that will be
	 * considered by the method toArray() if a key in
	 * $elements is not in $keysToArray then it will not
	 * be part of the array returned by toArray()
	 * when turning 
	 * @var unknown_type
	 */
	private $keysToArray = array();
	
	/**
	 * Avoid memory exhaustion due to recursive to array calls
	 *  
	 * @var unknown_type
	 */
	private static $instancesWhereToArrayMethodWasCalled = array();
	
	/**
	 * 
	 * @var unknown_type
	 */
	private $keysInElementsUsedAsArray = array();
	
	/**
	 * Lock the object to forbid Db input
	 * 
	 * @return unknown_type
	 */
	public function __construct()
	{
	}
	
	/**
	 * 
	 * @param unknown_type $key
	 * @return unknown_type
	 */
	public function isSetKey($key)
	{
		return isset($this->elements[$key]);
	}
	
	/**
	 * 
	 * @param $key
	 * @return unknown_type
	 */
	public function getElement($key)
	{
		if (!isset($this->elements[$key])) {
			throw new Exception('The element with key : ' . $key . ', is not set.');
		}
		return $this->elements[$key];
	}
	
	/**
	 * 
	 * @param $key
	 * @return unknown_type
	 */
	public function getElements()
	{
		return $this->elements;
	}
	
	/**
	 * 
	 * @param $key
	 * @param $value
	 * @param $keyInToArrayReturnArray
	 * @return unknown_type
	 */
	protected function setElement($key, $value, $keyInToArrayReturnArray = true)
	{	
		//if the key was already set, and it is exactly the same throw up 
		if (isset($this->elements[$key]) && $this->elements[$key] === $value) {
			throw new Exception("Element with key: '$key', is already set. with exactly the same value, code differently the value was and is : " . print_r($this->elements[$key], true));
		}

		$this->elements[$key] = $value;
		if (true === $keyInToArrayReturnArray) {
			$this->keysToArray[$key] = true;
		}
	}
	
	/**
	 * 
	 * @param unknown_type $key
	 * @return unknown_type
	 */
	protected function unsetElement($key)
	{
		if (isset($this->elements[$key])) {
			unset($this->elements[$key]);
		}
		if (isset($this->keysToArray[$key])) {
			unset($this->keysToArray[$key]);
		}
		if (isset($this->keysInElementsUsedAsArray[$key])) {
			unset($this->keysInElementsUsedAsArray[$key]);
		}
	}
	
	/**
	 * Create a Savable_Atom_Stack instance,
	 * under the key $key, and then push the value
	 * $value into the stack. If the value is not
	 * set from the same origin than the first value
	 * of the stack, the Savable_Atom_Stack::pushValue()
	 * method will throw an exception.
	 * However if $ifIsArrayMerge = true, this method will call
	 * Savable_Atom_Stack::mergeArray() instead which 
	 * will not necessarily throw up, it will depend on the
	 * value of $overwriteOrigin
	 * @see Savable_Atom_Stack::mergeArray() 
	 * for complete behaviour explanation
	 * @see //IMPOTANT NOTE IN BODY
	 * @param unknown_type $key
	 * @param unknown_type $value
	 * @return unknown_type
	 */
	protected function useKeyAsArrayAndPushValue($key, $value, $keyInToArrayReturnArray = true)
	{
		if (!isset($this->elements[$key])) {
			$this->elements[$key] = array();
			$this->keysInElementsUsedAsArray[$key] = true;
			if (true === $keyInToArrayReturnArray) {
				$this->keysToArray[$key] = true;
			}
		}
		if (!is_array($this->elements[$key])) {
			throw new Exception('You are trying to use a key as an array and it was previously not intended for that');
		}
		//cast values to same type when object
		if (!empty($this->elements[$key])) {
			$refTypeElement = current($this->elements[$key]);
			if (is_object($refTypeElement) && !($value instanceof $refTypeElement)) {
				throw new Exception('The passed value is not the same type as the last one you passed');
			}
		}
		$this->elements[$key][] = $value;
	}
	
	/**
	 * recursively create an array
	 * 
	 * @return unknown_type
	 */
	public function toArray()
	{	
		$finalArray = array();
		//only add the keys that are present in $this->keysToArray
		$elementsToArray = array_intersect_key($this->elements, $this->keysToArray);
		foreach ($elementsToArray as $key => $value) {
			if ($value instanceof self && $this->callToArrayOnInstance($value)) {
				$value = $value->toArray(); //returns an array
			} else if (isset($this->keysInElementsUsedAsArray[$key])
			         && (current($value) instanceof self)) {
				$array = array();
				foreach ($value as $v) {
					if ($v instanceof self && $this->callToArrayOnInstance($v)) {
						$array[] = $v->toArray();
					}
				}
				$value = $array;
			}
			$finalArray[$key] = $value;
		}
		return $finalArray;
	}
	
	/**
	 * Adds the instance to instances arrayed
	 * and returns false or true to let the caller
	 * know if it should call toArray() or not
	 * 
	 * @param $instance
	 * @return unknown_type
	 */
	private function callToArrayOnInstance($instance)
	{
		if (false === array_search($this, self::$instancesWhereToArrayMethodWasCalled, true)) {
			self::$instancesWhereToArrayMethodWasCalled[] = $instance;
			return true;
		}
		return false;
	}
}
