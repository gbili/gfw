<?php
namespace Gbili\Video\Entity\Savable;

use Gbili\Out\Out,
    Gbili\Db\ActiveRecord\ActiveRecordInterface,
    Gbili\Miner\Lexer\AbstractLexer,
    Gbili\Miner\Lexer\Exception,
    Gbili\Date\Year,
    Gbili\Country\Country,
    Gbili\Source\Source,
    Gbili\Url\Url,
    Gbili\Image\Savable              as ImageSavable,
    Gbili\Participant\Role\Savable   as RoleSavable,
    Gbili\Participant\Savable        as ParticipantSavable,
    Gbili\MIE\Savable                as MIESavable,
    Gbili\Video\Entity\Savable       as EntitySavable,
    Gbili\Video\Entity\Title\Savable as TitleSavable,
    Gbili\Video\Entity\Genre\Savable as GenreSavable,
    Gbili\Lang\ISO                   as LangISO;

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
	const TITLE = 2;
	const DATE = 3;
	const ACTOR = 4;
	const DIRECTOR = 5;
	const PRODUCER = 6;
	const TIME_LENGTH = 7;
	const COUNTRY = 8;
	const LANG = 9;
	const GENRE = 10;
	const TITLE_ORIGINAL = 11;
	const SOURCE = 12;
	const IMAGE = 13;
	const SYNOPSIS =14;
	
	/**
	 * 
	 * @param array $info
	 * @return unknown_type
	 */
	public function populateInstance(array $info)
	{
	    $instance = $this->getPopulableInstance();
		//force the instance to be of type:
		if (!($instance instanceof EntitySavable)) {
			throw new Exception('The instance is not an instance of Video_Entity_Savable');
		}
		//plug each final result into the current object
		foreach ($info as $entity => $value) {
			Out::l1("LEXER : ");
			if (!is_array($value) && !$this->isPlausibleValue($value)) {
				Out::l1("not plausible value in lexer const : $entity, value : $value \n");
				continue;
			}
			switch ($entity) {
				case self::TITLE;
					$instance->setTitle(new TitleSavable($value));
					//if title is set after image and before shared title, set image fileprefix with it
					if ($instance->getSharedInfo()->hasImage() && !$instance->getSharedInfo()->hasOriginalTitle()) {
						Out::l1("setting filerPefix from title\n");
						$instance->getSharedInfo()->getImage()->setFilePrefix($instance->getTitle()->getSlug()->getValue());
						$instance->getSharedInfo()->setAsUsingRecycledImage();
					}
					Out::l1("TITLE : {$instance->getTitle()->getSlug()->getValue()}\n");
					break;
				case self::DATE;
					if (!$this->isPlausibleValue($value)) {
						break;
					}
					$instance->getSharedInfo()->setDate(new Year($value));
					Out::l1("DATE : $value\n");
					break;
				case self::ACTOR;
					if (is_array($value)) {
						foreach ($value as $v) {
							if ($this->isPlausibleValue($v)) {
								$p = new ParticipantSavable(new RoleSavable('Actor'), new MIESavable($v), $instance->getSharedInfo());
								$instance->getSharedInfo()->addParticipant($p);
							}
						} 
					} else {
						$p = new ParticipantSavable(new RoleSavable('Actor'), new MIESavable($value), $instance->getSharedInfo());
						$instance->getSharedInfo()->addParticipant($p);
					}
					Out::l1("ACTOR : $value\n");
					break;
				case self::DIRECTOR;
					if (is_array($value)) {
						foreach ($value as $v) {
							if ($this->isPlausibleValue($v)) {
								$p = new ParticipantSavable(new RoleSavable('Director'), new MIESavable($v), $instance->getSharedInfo());
								$instance->getSharedInfo()->addParticipant($p);
							}
						} 
					} else {
						$p = new ParticipantSavable(new RoleSavable('Director'), new MIESavable($value), $instance->getSharedInfo());
						$instance->getSharedInfo()->addParticipant($p);
					}
					Out::l1("DIRECTOR : $value\n");
					break;
				case self::PRODUCER;
					if (is_array($value)) {
						foreach ($value as $v) {
							if ($this->isPlausibleValue($v)) {
								$p = new ParticipantSavable(new RoleSavable('Producer'), new MIESavable($v), $instance->getSharedInfo());
								$instance->getSharedInfo()->addParticipant($p);
							}
						}
					} else {
						$p = new ParticipantSavable(new RoleSavable('Producer'), new MIESavable($value), $instance->getSharedInfo());
						$instance->getSharedInfo()->addParticipant($p);
					}
					Out::l1("PRODUCER : $value\n");
					break;
				case self::TIME_LENGTH;
					
					break;
				case self::COUNTRY;
					$instance->getSharedInfo()->setCountry(new Country($value));
					if ($instance->getSharedInfo()->getCountry()->isNormalized()) {
						$instance->setLang(current($instance->getSharedInfo()->getCountry()->getLangs()));
					}
					Out::l1("COUNTRY : $value\n");
					break;
				case self::LANG;
					$instance->setLangISO(new LangISO($value));
					Out::l1("LANG : $value\n");
					break;
				case self::GENRE;
					$instance->getSharedInfo()->addGenre(new GenreSavable($value));
					Out::l1("GENRE : $value\n");
					break;
				case self::TITLE_ORIGINAL;
					$instance->getSharedInfo()->setOriginalTitle(new TitleSavable($value));
					//if the original title is set after image, set or change image file prefix with it
					if ($instance->getSharedInfo()->hasImage()) {
						//don't set prefix if it was already set with the same value
						if ($instance->hasTitle() && 
						   ($instance->getTitle()->getSlug()->getValue() === $instance->getSharedInfo()->getOriginalTitle()->getSlug()->getValue())) {
							Out::l1("not setting filerPefix from original title because it is same as title\n");
						   	break;
						}
						Out::l1("setting filerPefix from original title\n");
						$instance->getSharedInfo()->getImage()->setFilePrefix($instance->getSharedInfo()->getOriginalTitle()->getSlug()->getValue());
					}
					Out::l1("TITLE_ORIGINAL : " . $instance->getSharedInfo()->getOriginalTitle()->getSlug()->getValue() . "\n");
					break;
				case self::SOURCE;
					$instance->addSource(new Source($value));
					Out::l1("SOURCE : $value\n");
					break;
				case self::IMAGE;
					$i = new ImageSavable();
					$i->setSourceUrl(new Url($value));
					$instance->getSharedInfo()->setImage($i);
					//set image prefix from the title available, shared by pref
					if ($instance->getSharedInfo()->hasOriginalTitle()) {
						$i->setFilePrefix($instance->getSharedInfo()->getOriginalTitle()->getSlug()->getValue());
					} else if ($instance->hasTitle()) {
						$i->setFilePrefix($instance->getTitle()->getSlug()->getValue());
						$instance->getSharedInfo()->setAsUsingRecycledImage();
					}
					Out::l1("IMAGE : $value\n");
					break;
				case self::SYNOPSIS;
					$instance->setSynopsis($value);
					Out::l1("SYNOPSIS : $value\n");
					break;
				default;
					throw new Exception('The info array passed to populate instance apears not to be compliant');
					break;
			}
		}
	}
}