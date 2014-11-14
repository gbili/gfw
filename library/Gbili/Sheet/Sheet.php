<?php
namespace Gbili\Sheet;

use Gbili\Stdlib\ToStringInterface;

use Gbili\Sheet\SheetCollection\SheetCollection;

use Gbili\Line\Line;
use Gbili\Line\BlankLine;
use Gbili\Line\LineCollection\LineCollection;
use Gbili\Line\LineCollection\LineCollectionAwareInterface as LineCollectionAwareInterface;

class Sheet implements LineCollectionAwareInterface, FullInterface, ToStringInterface
{
    /**
     * The sheetCollection to which this sheet pertains
     * If null and sheet is full, add line will throw
     * Otherwise it will pass the excess lines to 
     * the sheetCollection
     * 
     * @var \Gbili\Sheet\SheetCollection
     */
    private $sheetCollection      = null;
    
    /**
     * 
     * @var integer
     */
    private $linesMaxCount   = null;
    
    /**
     * 
     * @var integer
     */
    private $linesMaxLength  = null;
    
    /**
     * 
     * @var boolean
     */
    private $reflowLongLines = false;
    
    /**
     * When lines are truncated and reflown, there may be an excess
     * of lines in the result, that could/should not be detected beforehand
     * This excess of lines, can either result in an Exception thrown or
     * can be handed on to the parent SheetCollection, such that the excess
     * can be put in another sheet. Which this sheet does not know about.
     * 
     * @var bool
     */
    private $allowPassOnOverflowToSheetCollection = true;
    
    /**
     * 
     * @var bool
     */
    private $fillEmptySpace = false;
    
    /**
     * 
     * @var Gbili\Line\LineCollection\LineCollection  
     */
    private $lineCollection  = null;
    
    /**
     *
     * @var Gbili\Line\LineCollection\LineCollection
     */
    private $overflowLineCollection = null;
    
    /**
     * 
     */
    public function __construct($config = null)
    {
        if (null !== $config) {
            if (is_array($config)) {
                $config = new Config($config);
            }
            if (!$config instanceof ConfigInterface) {
                throw new Exception("Constructor first param must either be an array or a ConfigInterface instance");
            }
            $config->configureSheet($this);
        }
    }
    
    /**
     * 
     * @return boolean
     */
    public function isEmpty()
    {
        return $this->getLineCollectionAsIs()->isEmpty();
    }
    
    /**
     * When new lines|collections|strings are added, if the
     * sheet is full, then the unadded parts are added to the
     * OverflowLineCollection, which is handed to the SheetCollection
     * so it can be refed to another sheet.
     * 
     * @param string|Line|LineCollection $line
     * @throws Exception
     * @return boolean
     */
    public function addLine($line)
    {
        if ($line instanceof LineCollection) {
            return $this->addLineCollection($line);
        } else if (is_string($line)) {
            $line = new Line($line);
        }
        
        if ($this->hasWidthConstraint() 
            && $line->getLength() > $this->getWidthConstraint()
        ) {
            return $this->splitLine($line);
        }
        
        if (!$this->isFull()) {
            $this->getLineCollectionAsIs()->add($line);
        } else {
            $this->getOverflowLineCollection()->add($line);
        }
        
        return !$this->isFull();
    }
    
    /**
     * 
     * @return \Gbili\Line\LineCollection\LineCollection
     */
    public function getOverflowLineCollection()
    {
        if (null === $this->overflowLineCollection) {
            $this->overflowLineCollection = new LineCollection();
        }
        return $this->overflowLineCollection;
    }
    
    /**
     * 
     * @return boolean
     */
    public function hasOverflowLineCollection()
    {
        return null !== $this->overflowLineCollection && !$this->overflowLineCollection->isEmpty();
    }
    
    /**
     * 
     * @param LineCollection $lc
     */
    public function addLineCollection(LineCollection $lc)
    {        
        foreach ($lc as $line) {
            $this->addLine($line);
        }
    }
    
    /**
     * 
     * @return boolean
     */
    public function hasSheetCollection()
    {
        return null !== $this->sheetCollection;
    }
    
    /**
     * 
     * @return \Gbili\Sheet\SheetCollection
     */
    public function getSheetCollection()
    {
        if (!$this->hasSheetCollection()) {
            throw new Exception("This sheet has not SheetCollection set, so cannot get it for the moment");
        }

        return $this->sheetCollection;
    }
    
    /**
     * 
     * @param SheetCollection $sheetCollection
     */
    public function setSheetCollection(SheetCollection $sheetCollection)
    {
        if ($this->hasSheetCollection()) {
            throw new Exception("This sheet already has a SheetCollection set, cannot reset it");
        }
        $this->sheetCollection = $sheetCollection;
    }
    
    /**
     * 
     * @param Line $line
     */
    protected function splitLine(Line $line)
    {
        if (!$this->isReflowLongLinesEnabled()) {
            throw new Exception("Line is too long, but split long lines is not enabled");
        }
        
        $chunkLen = $this->getWidthConstraint() - $line->getEOLSequenceLength();
        $splitLine = str_split($line->getContent(), $chunkLen);
        
        foreach ($splitLine as $chunk) {
            $chunkLine = new Line($chunk);
            $chunkLine->setEOLSequence($line->getEOLSequence());
            $this->addLine($chunkLine);
        }
    }
    
    /**
     * 
     * @param number $maxNumCharsPerLine
     * @return \Gbili\Sheet
     */
    public function setWidthConstraint($maxNumCharsPerLine)
    {
        if (!is_numeric($maxNumCharsPerLine)) {
            throw new Exception('setWidthConstraint($maxNumCharsPerLine) $maxNumCharsPerLine must be numeric');
        }
        $this->linesMaxLength = (integer) $maxNumCharsPerLine;
        return $this;
    }
    
    /**
     * 
     * @return number
     */
    public function getWidthConstraint()
    {
        if (!$this->hasWidthConstraint()) {
            throw new Exception("No width constraint is set for this sheet, set it before getting it");
        }
        return $this->linesMaxLength;
    }
    
    /**
     * 
     * @return boolean
     */
    public function hasWidthConstraint()
    {
        return null !== $this->linesMaxLength;
    }
    
    /**
     * 
     * @return boolean
     */
    public function isFull()
    {
        return $this->hasLineCountConstraint() && ($this->getLineCollectionAsIs()->count() >= $this->getLineCountConstraint());
    }
    
    /**
     * 
     * @return boolean
     */
    public function hasLineCountConstraint()
    {
        return null !== $this->linesMaxCount;
    }
    
    /**
     * 
     * @param unknown_type $number
     * @return \Gbili\Sheet\Sheet
     */
    public function setLineCountConstraint($number)
    {
        if (!is_numeric($number)) {
            throw new Exception('setLineCountConstraint($param), $param must be numeric');
        }
        $this->linesMaxCount = (integer) $number;
        return $this;
    }
    
    /**
     * 
     * @return number
     */
    public function getLineCountConstraint()
    {
        if (!$this->hasLineCountConstraint()) {
            throw new Exception("No line count constraint has been set for this sheet, set it before getting it");
        }
        return $this->linesMaxCount;
    }
    
    /**
     * 
     * @return \Gbili\Sheet
     */
    public function fillRemainingLines()
    {
        if (!$this->hasLineCountConstraint()) {
            return $this;
        }
        
        $linesMissingCount = $this->getLineCountConstraint() - $this->getLineCollectionAsIs()->count();
        
        while ($linesMissingCount-- > 0) {
            // Add BlankLines
            $this->getLineCollectionAsIs()->add();
        }
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see Gbili\Line\LineCollection.CollectionAwareInterface::getLineCollectionAsIs()
     * @todo add a configurable option named line_count for the fillRemainingLines() behavior
     */
    public function getLineCollection()
    {
        if (!$this->isFull()) {
            $this->fillRemainingLines();
        }
        return $this->getLineCollectionAsIs();
    }
    
    /**
     * 
     * @return \Gbili\Line\LineCollection\LineCollection
     */
    private function getLineCollectionAsIs()
    {
        if (null === $this->lineCollection) {
            $this->lineCollection = new LineCollection();
        }
        return $this->lineCollection;
    }
    
    /**
     * 
     * @param boolean $bool
     */
    public function setReflowLongLines($bool)
    {
        $this->reflowLongLines = (bool) $bool;
    }
    
    /**
     * 
     * @return boolean
     */
    public function isReflowLongLinesEnabled()
    {
        return $this->reflowLongLines;
    }
    
    /**
     * 
     */
    public function toString()
    {
        return $this->getLineCollectionAsIs()->toString();
    }
    
    /**
     * 
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getLineCollectionAsIs();
    }
}