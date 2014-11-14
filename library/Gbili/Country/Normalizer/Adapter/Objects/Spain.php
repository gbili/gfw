<?php
namespace Gbili\Country\Normalizer\Adapter\Objects;

class Spain 
extends AbstractObjects
{
	protected $regex = '/Espa\\p{L}\\p{M}*[ae]|Spa(?:nien|gna)|Spain/i';
	protected $langISO = array(International_LangISO::ES,
								International_LangISO::CA,
								International_LangISO::GL,
								International_LangISO::EU);
}