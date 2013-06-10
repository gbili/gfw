<?php
namespace Gbili\Navigation;

/**
 * 
 * @author gui
 *
 */
class SPageSelector
{

	/**
	 * Current Page Number
	 * 
	 * @var unknown_type
	 */
	private $currPageNum = null;
	
	/**
	 * total Items In Book
	 * 
	 * @var unknown_type
	 */
	private $totalIIB = null;
	
	/**
	 * Number of Items Per Page
	 * 
	 * @var unknown_type
	 */
	private $nIPP = 6;
	
	/**
	 * Number of Buttons In Nav
	 * 
	 * @var unknown_type
	 */
	private $nBIN = 5;
	
	/**
	 * Class used for css rendering
	 * 
	 * @var unknown_type
	 */
	private $cssSUPClass = array('this_page', 'other_page');
	
	/**
	 * 
	 * @var unknown_type
	 */
	private static $defaultSprintfFormat = '/page/%s/ipp/%s';
	
	/**
	 * 
	 * @var unknown_type
	 */
	private $sprintfFormat = null;
	
	/**
	 * 
	 * @var unknown_type
	 */
	private $prepUri = '';
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function __construct()
	{
		
	}
	
	/**
	 * 
	 * @param unknown_type $n
	 * @return unknown_type
	 */
	public function setCurrentPageNumber($n)
	{
		$this->currPageNum = $n;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getCurrentPageNumber()
	{
		if (null === $this->currPageNum) {
			throw new Exception('current page number must be set through setCurrentPageNumber($n)');
		}
		return $this->currPageNum;
	}

	/**
	 * 
	 * @param unknown_type $n
	 * @return unknown_type
	 */
	public function setNumberItemsInBook($n)
	{
		if (0 > $n) {
			throw new Exception('The number of items in book cannot be less than 0');
		}
		$this->totalIIB = $n;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getNumberItemsInBook()
	{
		if (null === $this->totalIIB) {
			throw new Exception('the total number of items in book must be set with : setNumberItemsInBook($n)');
		}
		return $this->totalIIB;
	}
	
	/**
	 * 
	 * @param unknown_type $n
	 * @return unknown_type
	 */
	public function setNumberOfItemsPerPage($n)
	{
		if (0 >= $n) {
			throw new Exception('The number of items per page cannot be less than or equal to 0');
		}
		$this->nIPP = $n;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getNumberOfItemsPerPage()
	{
		return $this->nIPP;
	}
	
	/**
	 * 
	 * @param unknown_type $n
	 * @return unknown_type
	 */
	public function setNumberOfButtonsInNav($n)
	{
		$this->nBIN = $n;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getNumberOfButtonsInNav()
	{
		return $this->nBIN;
	}
	
	/**
	 * 
	 * @param unknown_type $str
	 * @return unknown_type
	 */
	public function setCssSUPageClasses($selectedClassStr, $unselectedClassStr)
	{
		$this->cssSUPClasses = array($selectedClassStr, $unselectedClassStr);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getCssSUPageClasses()
	{
		return $this->cssSUPClasses;
	}
	
	/**
	 * 
	 * @param $format
	 * @return unknown_type
	 */
	public function setSprintfFormat($format)
	{
		$this->sprintfFormat = $format;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getSprintfFormat()
	{
		if (null === $this->sprintfFormat) {
			$this->sprintfFormat = self::$defaultSprintfFormat;
		}
		return $this->sprintfFormat;
	}
	
	/**
	 * 
	 * @param $format
	 * @return unknown_type
	 */
	public static function setDefaultSprintfFormat($format)
	{
		self::$defaultSprintfFormat = $format;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public static function getDefaultSprintfFormat()
	{
		return self::$defaultSprintfFormat;
	}
	
	/**
	 * 
	 * @param unknown_type $str
	 * @return unknown_type
	 */
	public function setPrependedUri($str)
	{
		$this->prepUri = $str;
	}
	
	/**
	 * 
	 * @param unknown_type $str
	 * @return unknown_type
	 */
	public function getPrependedUri($str)
	{
		return $this->prepUri;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function render()
	{
		//make sure $this->sprintfFormat has something
		$this->getSprintfFormat();
		$html = '';

		$totalAmountOfPages = ceil($this->getNumberItemsInBook() / $this->getNumberOfItemsPerPage());
		
		/*
		 * Only render navigator if there is more than one page
		 */
		if ($totalAmountOfPages <= 1) {
			return $html;
		}
		
		/*
		 * Determine whether to show the "<<.. <" (go to first, go to previous) portion of the navigation
		 * When ? -- Only if we are not on the first page
		 */
		if ($this->getCurrentPageNumber() > 1) {//if the user is viewing a page bigger than the first show <<.. link
			$html .= $this->renderHtmlButton($this->cssSUPClass[1], 1, '<<...');
			$html .= $this->renderHtmlButton($this->cssSUPClass[1], ($this->currPageNum - 1), '<');
		}
		
		/*
		 * Render the buttons for each page: this portion of the navigation:
		 *  |  1  | |  2  | |  3  | |  4  | |  5  |
		 *  When ? -- In any situation
		 */
		$pagesLeft = $totalAmountOfPages - $this->currPageNum;
		if ($this->currPageNum <= ($this->nBIN - 2) || $totalAmountOfPages <= $this->nBIN) {//until <-cond is true
			$forStart = 1;
			$forCount = min($this->nBIN, $totalAmountOfPages);//don't show more buttons than pages available		
		} else if ($pagesLeft >= $this->nBIN) {//if there are more pages left than the number of buttons in nav + previous condition (we are not on the first pages)
			$forStart = $this->currPageNum - 2;//render the nav with the currentPage button positioned two buttons from the left
			$forCount = $forStart + $this->nBIN - 1;//show the buttons for the next x pages
			
		} else {//if we are on the last pages
			$forStart = ($totalAmountOfPages + 1) - $this->nBIN;//but start counting so that there are allways the number of buttons per page in nav
			$forCount = $totalAmountOfPages;//show the rest of pages
		}
		
		for ($i=$forStart; $i<= $forCount; $i++) { 
			$html .= $this->renderHtmlButton(((integer) $i === (integer) $this->currPageNum)? $this->cssSUPClass[0]: $this->cssSUPClass[1], $i, $i);
		}
		
		/*
		 * Determine whether to render this protion "> ..>>" (go to next, go to end)
		 * When ? -- Only if we are not in the last page
		 */
		if ($this->currPageNum < $totalAmountOfPages) {
			$html .= $this->renderHtmlButton($this->cssSUPClass[1], ($this->currPageNum + 1), '>');
			$html .= $this->renderHtmlButton($this->cssSUPClass[1], $totalAmountOfPages, '...>>');
		}
		return $html;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	private function renderHtmlButton($class, $pageNum, $value)
	{
		$url = sprintf($this->sprintfFormat, $pageNum, $this->nIPP);
		return "<div class=\"$class\"><a href=\"{$this->prepUri}{$url}\">$value</a></div>";
	}
	
}