<?php
namespace Gbili\Regex\String;

class LangISO 
extends AbstractString
{
	/**
	 * 
	 * @var unknown_type
	 */
	protected $defaultRegex = '^(en|de|fr|it|pt|eu|ca|da)$';
	
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