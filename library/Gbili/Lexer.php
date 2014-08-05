<?php
namespace Gbili;

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
class Lexer
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
	    $instance = $this->getPopulableInstance();
		//force the instance to be of type:
		if (!($instance instanceof VidSavable)) {
			throw new Exception('The instance is not an instance of Video_Entity_Savable');
		}
		//plug each final result into the current object
		foreach ($info as $entity => $value) {
			//Out::l1("LEXER : ");
			if (!is_array($value) && !$this->isPlausibleValue($value)) {
				//Out::l1("not plausible value in lexer const : $entity, value : $value \n");
				continue;
			}
			switch ($entity) {
				case self::TITLE;
					$instance->setTitle(new TitleSavable($value));
					//if title is set after image and before shared title, set image fileprefix with it
					if ($instance->hasImage()) {
						//Out::l1("setting filerPefix from title\n");
						$instance->getImage()->setFilePrefix($instance->getTitle()->getSlug()->getValue());
					}
					//Out::l1("TITLE : {$instance->getTitle()->getSlug()->getValue()}\n");
				break;
				case self::CATEGORY;
					$instance->setCategory(new CategorySavable($value));
					//Out::l1("CAT : {$instance->getCategory()->getValue()}\n");
				break;
				//@TODO add tag handler from phrase
				case self::TAG;
					$instance->addTag(new TagSavable($value));
					//Out::l1("TITLE : {$instance->getTitle()->getSlug()->getValue()}\n");
				break;
				case self::DATE;
					$instance->setDate(new AgoToDate($value));
					//Out::l1("DATE : $value\n");
				break;
				case self::TIME_LENGTH;
					$t = new TimeLengthStrToInt($value);
					$instance->setTimeLength($t->toString());
					//Out::l1("TIME_LENGTH : $value\n");
				break;
				case self::SOURCE;
					$instance->setSource(new SourceSavable(new Url($value)));
					Out::l1("SOURCE - entity: $entity, value: $value\n");
				break;
				case self::IMAGE;
					$i = new ImageSavable();
					$i->setSourceUrl(new Url($value));
					$instance->setImage($i);
					//set image prefix from the title available, shared by pref
					if ($instance->hasTitle()) {
						$i->setFilePrefix($instance->getTitle()->getSlug()->getValue());
					}
					//Out::l1("IMAGE - entity: $entity, value: $value\n");
				break;
				case self::HOST_NAME;
    				if (!$instance->hasSource()) {
    				    echo 'no source, so we we\'ll have to wait until source is set, we will be called again don\'t worry ;)';
    				    $this->setDependency(self::SOURCE, self::HOST_NAME, $value);
    				} else {
    				    $instance->getSource()->getHost()->setUserFriendlyName($value);
    				}
					//Out::l1("HOST_NAME : $value\n");
				break;
				default;
					throw new Exception('The info array passed to populate instance apears not to be compliant' . print_r($info, true));
				break;
			}
		}
	}
}
