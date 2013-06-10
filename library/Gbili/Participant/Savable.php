<?php
namespace Gbili\Participant;

use Gbili\Savable\Exception;

/**
 * This is the conjunction of 
 * 	-a MIE (which identifies a person)
 *  -a Video_Entity (which identifies a movie)
 *  -a involvement type (a role name)
 *  which results in a participant
 *  that means:
 *  the role of a MIE in a certain Movie
 * 
 * @author gui
 *
 */
class Savable
extends \Gbili\Savable\Savable
{	
	
	/**
	 * 
	 * @param Participant_Role_Savable $role
	 * @param MIE_Savable $mIE
	 * @param Video_Entity_Savable $vE
	 * @return void
	 */
	public function __construct(Role\Savable $role,
								\Gbili\MIE\Savable $mIE,
								\Gbili\Video\Entity\SharedInfo\Savable $sH)
	{
		parent::__construct();
		$this->setElement('role', $role);
		$this->setElement('mIE', $mIE);
		$this->setElement('sharedInfo', $sH);
	}

	/**
	 * 
	 * @return unknown_type
	 */
	public function getRole()
	{
		return $this->getElement('role');
	}
	
	/**
	 * Mie must be saved before participant can
	 * 
	 * @return unknown_type
	 */
	public function getMIE()
	{
		return $this->getElement('mIE');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getSharedInfo()
	{
		return $this->getElement('sharedInfo');
	}
}