<?php
namespace Gbili\Db\ActiveRecord;

/**
 * This interface is used in Engine
 * 
 * @author gui
 *
 */
interface ActiveRecordInterface
{	
	/**
	 * Whenever there is data to update new fields to insert
	 * call this.
	 * 
	 * Important it needs a reference;
	 * 
	 * @param Object_Abstract $instance
	 * @return unknown_type
	 */
	public function save();
	
	/**
	 * Delete what is in database
	 * 
	 * @param Object_Abstract $instance
	 * @return unknown_type
	 */
	public function delete();
	
	/**
	 * Gets or generates an id from the bare minimum info
	 * 
	 * @param Object_IdElement $instance
	 * @return unknown_type
	 */
	public function getId();
}