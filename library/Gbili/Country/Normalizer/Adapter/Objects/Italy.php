<?php
namespace Gbili\Country\Normalizer\Adapter\Objects;

class Italy 
extends AbstractObjects
{
	protected $regex = '/(?:It\\p{L}\\p{M}*l[yi](?:a|(?:en))?)/i';
	protected $langISO = array(International_LangISO::IT);
}