<?php
namespace Gbili\Regex\String;

class Generic
extends AbstractString
{
	/**
	 * 
	 * @var unknown_type
	 */
	protected $defaultRegex = '.*';
	
	/**
	 * Only called 
	 * 
	 * (non-PHPdoc)
	 * @see Common/Regex/AbstractString#getUpdatedRegex()
	 */
	protected function update()
	{
	}
}