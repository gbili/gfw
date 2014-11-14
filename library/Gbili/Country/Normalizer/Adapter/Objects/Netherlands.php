<?php
namespace Gbili\Country\Normalizer\Adapter\Objects;

class Netherlands 
extends AbstractObjects
{
	protected $regex = '/Pays-Bas|Pa\\p{L}\\p{M}*ses-Ba(?:j|ix)os|Paesi-Bassi|Niederlande|Netherlands/i';
	protected $langISO = array(International_LangISO::NL);
}