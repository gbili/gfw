<?php
namespace Gbili\Country\Normalizer\Adapter\Objects;

class Switzerland 
extends AbstractObjects
{
	protected $regex = '/Switzerland|Suisse|Suiza|Svizzera|Schweiz|Su\\p{L}\\p{M}*\\p{L}\\p{M}*a/i';
	protected $langISO = array(International_LangISO::FR,
								International_LangISO::DE,
								International_LangISO::IT);
}