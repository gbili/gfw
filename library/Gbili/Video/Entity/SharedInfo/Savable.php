<?php
namespace Gbili\Video\Entity\SharedInfo;

use Gbili\Date\AbstractDate,
    Gbili\Image\Savable       as ImageSavable,
    Gbili\Participant\Savable as ParticipantSavable,
    Gbili\Genre\Savable       as GenreSavable,
    Gbili\Title\Savable       as TitleSavable,
    Gbili\Country\Country;

/**
 * Contains info shared among
 * Video_Entity_Savable(ies) of
 * the same Originla video entity
 * 
 * This class avoids data using a
 * lot of memory because of data
 * repetition
 * 
 * The question is: 
 * Should it extend Savable_Abstract?
 * 
 * @author gui
 *
 */
class Savable
extends \Gbili\Savable\Savable
{	
	/**
	 * 
	 * @return unknown_type
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getDate()
	{
		return $this->getElement('date');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasDate()
	{
		return ($this->isSetKey('date'));
	}

	/**
	 * 
	 * @param Country $country
	 * @return unknown_type
	 */
	public function setDate(AbstractDate $date)
	{
		if (!$date->isValid()) {
			throw new Exception('date is not valid');
		}
		$this->setElement('date', $date->toString());
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function setImage(ImageSavable $img)
	{
		$this->setElement('image', $img);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function isUsingRecycledImage()
	{
		if (!$this->hasImage()) {
			throw new Exception('There is no image for this shared info instance so cannot determine whether it is recycled or not');
		}
		//default value
		if (!$this->isSetKey('isUsingRecycledImage')) {
			$this->setElement('isUsingRecycledImage', 0);
		}
		return $this->getElement('isUsingRecycledImage');
	}
	
	/**
	 * 
	 * @param unknown_type $boolean
	 * @return unknown_type
	 */
	public function setAsUsingRecycledImage()
	{
		$this->setElement('isUsingRecycledImage', 1);
	}
	
	
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasImage()
	{
		return $this->isSetKey('image');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getImage()
	{
		return $this->getElement('image');
	}
	
	/**
	 * 
	 * @return Country
	 */
	public function getCountry()
	{
		return $this->getElement('country');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasCountry()
	{
		return $this->isSetKey('country');
	}

	/**
	 * 
	 * @param Country $country
	 * @return unknown_type
	 */
	public function setCountry(Country $country)
	{
		$this->setElement('country', $country);
	}
	
	/**
	 * 
	 * @param Participant $participant
	 * @return unknown_type
	 */
	public function addParticipant(ParticipantSavable $participant)
	{
		$this->useKeyAsArrayAndPushValue('participants', $participant, \Gbili\Savable\Savable::POST_SAVE_LOOP);
	}
	
	/**
	 * 
	 * @param unknown_type $genre
	 * @return unknown_type
	 */
	public function addGenre(GenreSavable $genre)
	{
		$this->useKeyAsArrayAndPushValue('genre', $genre);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getGenres()
	{
		return $this->getElement('genre');
	}

	/**
	 * 
	 * @return unknown_type
	 */
	public function hasGenre()
	{
		return $this->isSetKey('genre');
	}

	/**
	 * 
	 * @param unknown_type $timeLength
	 * @return unknown_type
	 */
	public function setTimeLength($timeLength)
	{
		$this->setElement('timeLength', $timeLength);
	}

	/**
	 * 
	 * @return unknown_type
	 */
	public function getTimeLength()
	{
		return $this->getElement('timeLength');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasTimeLength()
	{
		return $this->isSetKey('timeLength');
	}
	
	/**
	 * 
	 * @param unknown_type $originalTitle
	 * @return unknown_type
	 */
	public function setOriginalTitle(TitleSavable $originalTitle)
	{
		$this->setElement('originalTitle', $originalTitle);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getOriginalTitle()
	{
		return $this->getElement('originalTitle');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasOriginalTitle()
	{
		return $this->isSetKey('originalTitle');
	}
}