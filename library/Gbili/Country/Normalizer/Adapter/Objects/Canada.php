<?php
namespace Gbili\Country\Normalizer\Adapter\Objects;

class Canada extends AbstractObjects
{
	protected $regex = '/[CK]anad\\p{L}\\p{M}*/i';
	protected $langISO = array(International_LangISO::EN,
								International_LangISO::FR);
}