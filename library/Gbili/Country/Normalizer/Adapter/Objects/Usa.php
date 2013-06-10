<?php
namespace Gbili\Country\Normalizer\Adapter\Objects;

class Usa 
extends AbstractObjects
{
	protected $regex = '/u\\.?s\\.?a\\.?|e\\.?e\\.?u\\.?u\\.?|Vereinigte[- _.]Staa?ten|Estados[- _.]Un\\p{L}\\p{M}*dos.*?(?:Am\\p{L}\\p{M}*rica)?|United[- _.]states.*?(?:America)?|\\p{L}\\p{M}*tats[-_. ]unis[-_. ]d?.?Am\\p{L}\\p{M}*rique|Stati[ -_.]Uniti(?:[ -_.]d.america)?/i';
	protected $langISO = array(International_LangISO::EN);
}