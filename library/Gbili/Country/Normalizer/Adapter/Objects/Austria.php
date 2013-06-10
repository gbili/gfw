<?php
namespace Gbili\Country\Normalizer\Adapter\Objects;

class Austria extends AbstractObjects
{
	protected $regex = '/\\p{L}\\p{M}*e?sterreich|Au(?:stria|triche)/i';
	protected $langISO = array(International_LangISO::DE);
}