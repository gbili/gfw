<?php
namespace Gbili\Country\Normalizer\Adapter\Objects;

class Uk 
extends AbstractObjects
{
	protected $regex = '/[UV]\\.?K\\.?|United Kingdom|Britain|Royaume-Uni|Re[ig]no Uni[dt]o|Vereinigtes K(?:\\p{L}\\p{M}*|oe)nigreich/i';
	protected $langISO = array(International_LangISO::EN);
}