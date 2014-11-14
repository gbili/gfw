<?php
namespace Gbili\Regex\String;

class Attribute 
extends AbstractString
{
	/**
	 * 
	 * @var unknown_type
	 */
	protected $defaultRegex = '.*?=("??)([^" >=]*?)\\1';
	
	/**
	 * Only called 
	 * 
	 * (non-PHPdoc)
	 * @see Common/Regex/AbstractString#getUpdatedRegex()
	 */
	private function update()
	{
	}
}