<?php
namespace Gbili\Country\Normalizer\Adapter\Objects;

class Australia
extends AbstractObjects
{
	protected $regex = '/Austr\\p{L}\\p{M}*li(?:a|en)/i';
	protected $langISO = array(International_LangISO::EN);
}