<?php
namespace Gbili\Miner\Blueprint\Action\GetContents;

/**
 * Used to fetch contents from the internet and save them
 * to the database
 * 
 * @author gui
 *
 */
interface ContentsInterface 
{

	public function __construct();
	
	/**
     * Tells whether a url has been passed to it
     * through setUrl()
	 * 
	 * @return boolean
	 */
	public function hasUrl();
	
	/**
	 * Set the url that will be used to fetch the content 
     *
	 * @param \Gbili\Url\Url $url
	 */
	public function setUrl(\Gbili\Url\Url $url);
	
	/**
	 * 
	 * @return \Gbili\Url\Url
	 */
	public function getUrl();
	
	/**
	 * 
	 * @param string $contents the contents that have been fetched
	 */
	public function setContents($contents);
	
	/**
	 * Try to get contents from db or web, if
	 * success set contents in instance, or just
	 * return $contents containing false
	 * 
	 * @return string|booleanl
	 */
	public function getContents();
	
	/**
	 * 
	 * @return boolean 
	 */
	public function hasContents();
}
