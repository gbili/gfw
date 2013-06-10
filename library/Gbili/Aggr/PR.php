<?php
/**
 * PR (Public Relations)
 * This is a class that should be used to modify 
 * aggregation's contents
 * 
 * 
 * 
 * 
 * @author Gui
 *
 */
class Aggr_PR
{
	
	/**
	 * 
	 * 
	 * @var Aggr_Abstract
	 */
	private $aggr = null;
	
	/**
	 * 
	 * @param int $aggr aggregation id or instance, if id instance is created
	 * @param int $srcUId the person from the aggregation
	 * that invites or confirms, OR the person that requests 
	 * for involvement
	 * @return void
	 */
	public function __construct ( $aggr, $srcUId ) 
	{
		if (is_int($aggr)) {
			$inst = Aggr_Abstract::factory($aggr);
			if (false !== $inst) {
				$this->aggr = $inst;
			}
		}
	}
	
	/**
	 * 
	 * @param unknown_type $destUId
	 * @return unknown_type
	 */
	public function invite ( $destUId ) 
	{
		//check if the srcUId has some power over the aggrId passed on construct
		if (! Aggr_Db::hasPowerOnAggr($this->srcUId, $this->aggrId)) {
			//Aggr_Db::
		}
		
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function request () {}
	
	/**
	 * 
	 * @param int $destUId the user that has sent the request
	 * @return unknown_type
	 */
	public function confirm ( $destUId ) {}
	
}