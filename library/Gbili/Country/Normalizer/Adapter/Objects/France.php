<?php
namespace Gbili\Country\Normalizer\Adapter\Objects;

class France extends AbstractObjects
{
	protected $regex = '/Fran\\p{L}\\p{M}*(?:ia|[ea]|kreich)/i';
	protected $langISO = array(International_LangISO::FR);
}