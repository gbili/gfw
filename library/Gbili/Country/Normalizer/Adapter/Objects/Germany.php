<?php
namespace Gbili\Country\Normalizer\Adapter\Objects;

class Germany 
extends AbstractObjects
{
	protected $regex = '/German(?:y|ia)|Aleman[ih]a|Allemagne|Deutschland/i';
	protected $langISO = array(International_LangISO::DE);
}