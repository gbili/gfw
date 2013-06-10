<?php
namespace Gbili\Country\Normalizer\Adapter\Objects;

class Belgium 
extends AbstractObjects
{
	protected $regex = '/B\\p{L}\\p{M}*lgi(?:um|que|ca|en)/i';
	protected $langISO = array(International_LangISO::FR);
}