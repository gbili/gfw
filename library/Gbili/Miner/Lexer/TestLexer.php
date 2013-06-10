<?php
namespace Gbili\Miner\Lexer;

use Gbili\Time\Length\StrToInt as TimeLengthStrToInt;
use Gbili\Miner\Lexer\AbstractLexer;
use Gbili\Miner\Lexer\Exception;
use Gbili\Out\Out;
use Gbili\Db\ActiveRecord\ActiveRecordInterface;
use Gbili\Vid\Savable          as VidSavable;
use Gbili\Vid\Tag\Savable      as TagSavable;
use Gbili\Vid\Title\Savable    as TitleSavable;
use Gbili\Vid\Image\Savable    as ImageSavable;
use Gbili\Vid\Category\Savable as CategorySavable;
use Gbili\Time\AgoToDate;
use Gbili\Url\Url;
use Gbili\Source\Savable       as SourceSavable;

/**
 * This will populate the video entites
 * with the data in the way specified in populateInstance();
 * @author gui
 *
 */
class TestLexer
extends AbstractLexer
{
	
	/**
	 * 
	 * @var unknown_type
	 */
	const TITLE       = 2;
	const DATE        = 3;
	const TIME_LENGTH = 7;
	const CATEGORY    = 10;
	const TAG         = 11;
	const SOURCE      = 12;
	const IMAGE       = 13;
	const HOST_NAME   = 14;
	
	/**
	 * 
	 * @param array $info
	 * @return unknown_type
	 */
	public function populateInstance(array $info)
	{
		//plug each final result into the current object
		foreach ($info as $entity => $value) {
			////Out::l1("LEXER : ");
			if (!is_array($value) && !$this->isPlausibleValue($value)) {
				//Out::l1("not plausible value in lexer const : $entity, value : $value \n");
				continue;
			}
			switch ($entity) {
				case self::TITLE;
					//Out::l1("TITLE : {$value}\n");
				break;
				case self::CATEGORY;
					//Out::l1("CAT : {$value}\n");
				break;
				//@TODO add tag handler from phrase
				case self::TAG;
					//Out::l1("TITLE : {$value}\n");
				break;
				case self::DATE;
					//Out::l1("DATE : $value\n");
				break;
				case self::TIME_LENGTH;
					//Out::l1("TIME_LENGTH : $value\n");
				break;
				case self::SOURCE;
					//Out::l1("SOURCE - entity: $entity, value: $value\n");
				break;
				case self::IMAGE;
					//Out::l1("IMAGE - entity: $entity, value: $value\n");
				break;
				case self::HOST_NAME;
					//Out::l1("HOST_NAME : $value\n");
				break;
				default;
					throw new Exception('The info array passed to populate instance apears not to be compliant' . print_r($info, true));
				break;
			}
		}
	}
}
