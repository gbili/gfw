<?php
namespace Gbili\Country\Normalizer\Adapter\Objects;

class Portugal 
extends AbstractObjects
{
	protected $regex = '/Portugal|Portogallo/i';
	protected $langISO = array(International_LangISO::PT);
}