<?php
namespace Gbili\Country\Normalizer\Adapter\Objects;

class Argentina 
extends AbstractObjects
{
	protected $regex = '/Argentin(?:[ae]|ien)/i';
	protected $langISO = array(International_LangISO::ES);
}