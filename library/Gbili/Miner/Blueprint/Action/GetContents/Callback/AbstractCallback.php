<?php
namespace Gbili\Miner\Blueprint\Action\GetContents\Callback;

/**
 * The callback classess must extend this one in order to be considered
 * by the action execution
 * 
 * This will create an array that will be thought as callback() arguments
 * from a numerically indexed array which values are groups that map to the keys
 * of the second array which values are the actual input data for callback
 * The purpose of this array creation is to ensure the callback will
 * know what means what. as when you would call func_get_args()
 * 
 * Every action gets an input and produces a result. Here the thing is
 * to get that result which can be of two sorts : 
 * 1. -array of groups => results produced from an extract on the second sort of result,
 * 2. -string containing any type of info, from url to file_get_contents result
 * when the first case arises the input array(group=>result, group=>result, etc)
 * is passed to puzzleValuesWithKeys() along with $paramToGroupsArray wich maps
 * each group contained in the input array to an hypothetical parameter position
 * in callback() function.
 * As you can see callback takes a numerically indexed array as input and every
 * position starting from 0 is considered an argument (somewhat of a protocol, that
 * is determined between the developer of the Miner_Persistance_Blueprint_Action_GetContents_Callback_Abstract
 * subclass and the developer of the action that which result is used as input)
 * What happens when the second case arises, and the input is a string, "the callback
 * only takes arrays as input...", then callback wraps that result into an array(0=>string)
 * and it can be thought as the first parameter like in the first case.
 * 
 * Once that array(argumentPosition=>argumentInput) is created it is passed to
 * callback (the one implemented by the subclass) that is suposed to produce an output
 * that will be injected as the action input which must be a string
 * 
 * 
 * 
 * 
 * @author gui
 *
 */
abstract class AbstractCallback
{	
	/**
	 * Is used to map each key in input to
	 * a param position in callback call
	 * 
	 * Contains an array of numerically indexed
	 * keys from 0 to l-1, (corresponding to the param
	 * position in the callback() call from the
	 * Miner_Persistance_Blueprint_Action_GetContents_Callback_Abstract
	 * extending class) pointing to the group
	 * of the results from the parent action,
	 * that are passed to this instance when calling
	 * callback(<parent action results>).
	 * 
	 * 
	 * @var unknown_type
	 */
	private $paramToGroupArrayArray = null;
	
	/**
	 * 
	 * @var unknown_type
	 */
	private $loopReachedEndArray = array();
	
	/**
	 * 
	 * @var unknown_type
	 */
	private $loopIsFirstTime = array();
	
	/**
	 * 
	 * @return unknown_type
	 */
    public function __constrcut()
    {
    }
	
	/**
	 * 
	 * @param $bool
	 * @return unknown_type
	 */
	public function setLoopReachedEndState($bool)
	{
		$this->loopReachedEndArray = (boolean) $bool;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getLoopReachedEndState()
	{
		if (!isset($this->loopReachedEndArray)) {
			throw new Exception(get_class($this) . " did not set the loopReachedEndState, maybe the method was not called yet or it is not calling setLoopReachedEndState((boolean) \$bool) after executing.");		
		}
		return $this->loopReachedEndArray;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function setLoopIsFirstTime($bool)
	{
		$this->loopFirstTime = (boolean) $bool;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function isLoopFirstTime()
	{
		return $this->loopFirstTime;
	}
}
