<?php
namespace Gbili\Sheet\SheetCollection;

use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Gbili\Stdlib\Collection;
use Gbili\Stdlib\Matrix;
use Gbili\Line\LineCollection\LineCollection;
use Gbili\Sheet\SheetCollection\Formatter\FormatterInterface;
use Gbili\Sheet\FullInterface;
use Gbili\Sheet\Sheet;
use Gbili\Sheet\SheetCollection\SheetCollectionAwareInterface;


class SheetCollection extends Collection
{
    /**
     * 
     * @var \Gbili\Sheet\SheetCollection\ConfigInterface
     */
    protected $config          = null;
    
    /**
     * 
     * @var \Gbili\Stdlib\Matrix
     */
    protected $matrix          = null;
    
    /**
     * 
     * @var number
     */
    protected $matrixColumnCount   = 1;
    
    /**
     * 
     * @var \Zend\EventManager\EventManagerInterface
     */
    protected $events;
    
    /**
     * 
     * @var bool
     */
    protected $isFormatted    = false;
    
    /**
     * 
     * @var \Gbili\Sheet\SheetCollection\Formatter\FormatterInterface
     */
    protected $formatter      = null;
    
    /**
     * 
     * @var \Gbili\Line\LineCollection\LineCollection
     */
    protected $lineCollection = null;
    
    /**
     * 
     * @var \Gbili\Sheet\Sheet
     */
    protected $sampleSheet    = null;
    
    
    /**
     *
     * @param LineCollection $lineCollection
     */
    public function setLineCollection(LineCollection $lineCollection)
    {
        $this->lineCollection = $lineCollection;
    }
    
    /**
     *
     * @param LineCollection $lines
     */
    public function addToLineCollection(LineCollection $lines)
    {
        if (!$this->hasLineCollection()) {
            $this->setLineCollection($lines);
        } else {
            $this->getLineCollection()->push($lines);
        }
    }
    
    /**
     *
     * @return boolean
     */
    public function hasLineCollection()
    {
        return null !== $this->lineCollection;
    }
    
    /**
     *
     * @return \Gbili\Line\LineCollection\LineCollection
     */
    public function getLineCollection()
    {
        if (!$this->hasLineCollection()) {
            throw new Exception("No LineCollection has been set for this SheetCollection");
        }
        return $this->lineCollection;
    }
    
    /**
     * 
     * @return \Zend\EventManager\EventManagerInterface
     */
    public function getEventManager()
    {
        if (null === $this->events) {
            $this->setEventManager(new EventManager(
                array(__CLASS__, get_called_class())
            ));
        }
        return $this->events;
    }
    
    /**
     * 
     * @param EventManagerInterface $events
     */
    public function setEventManager(EventManagerInterface $events)
    {
        $this->events = $events;
    }
    
    /**
     * Configure the collection, and all its members
     * 
     * @param unknown_type $config
     */
    public function setConfig($config)
    {
        if (is_array($config)) {
            $config = new Config($config);
        }

        if (!$config instanceof Config) {        
            throw new Exception("You must either pass an array with config keys or a Gbili\Sheet\SheetCollection\Config instance");
        }
        
        $this->config = $config;
        
        $config->configureSheetCollection($this);
        
        if (null === $this->getMatrixColumnCount()) {
            throw new Exception(" Row sheet count is mandatory");
        }

    }
    
    /**
     * 
     * @return \Gbili\Sheet\SheetCollection\ConfigInterface
     */
    public function getConfig()
    {
        if (null === $this->config) {
            throw new Exception("No config instance has been set as \$this->config property");
        }
      /*if (^) {
            $this->config = new Config();
        }*/
        return $this->config;
    }
    
    /**
     * 
     * @param integer $count
     */
    public function setMatrixColumnCount($count)
    {
        if (!is_numeric($count)) {
            throw new Exception("Count must be numeric");
        }
        if (1 > $count) {
            throw new Exception("Count must be numeric");
        }
        $this->matrixColumnCount = (integer) $count;
    }
    
    /**
     * 
     * @param FormatterInterface $f
     */
    public function setFormatter(FormatterInterface $f)
    {
        $this->formatter = $f;
    }
    
    /**
     * 
     * @return \Gbili\Sheet\SheetCollection\Formatter\FormatterInterface
     */
    public function getFormatter()
    {
        if (null === $this->formatter) {
            throw new Exception("No formatter has been set as \$this->formatter property");
        }
        return $this->formatter;
    }
    
    /**
     * 
     */
    public function format()
    {
        if (false === $this->isFormatted) {
            $this->getFormatter()->format();
            $this->isFormatted = true;
        }
    }
    
    /**
     * Returns a sheet pertaining to the collection
     * Allways get new sheets from here
     * 
     * @return \Gbili\Sheet\Sheet
     */
    public function getSheet()
    {
        if (1 !== $this->getMatrixColumnCount()) {
            throw new Exception("Formatter must use getSheetRow() instead of " . __METHOD__);
        }
        return current($this->getSheetRow());
    }
    
    /**
     * 
     * @param number $count
     * @return Ambigous <\Gbili\Sheet\FullInterface, boolean, \Gbili\Stdlib\mixed>|\Gbili\Sheet\Sheet
     */
    public function getSheetRow()
    {
        if ($this->isEmpty()) {
            $this->getMatrix()->addRow($this->getEmptySheetRow());
            return $this->getMatrix()->getImaginaryRow();
        }
        
        $lastSheetRow    = $this->getMatrix()->getImaginaryRow();
        $sheetCount      = $this->getMatrixColumnCount();

        for ($col=0; $col < $sheetCount; $col++) {
            $this->handleAdvancementsAndOverflows($lastSheetRow, $col);
        }
        return $this->getMatrix()->getImaginaryRow();
    }
    
    /**
     * How many elements per row in matrix?
     * 
     * @return number
     */
    protected function getMatrixColumnCount()
    {
        return $this->matrixColumnCount;
    }

    /**
     *
     * @return \Gbili\Stdlib\Matrix
     */
    protected function getMatrix()
    {
        if (null === $this->matrix) {
            $this->matrix = new Matrix($this->getMatrixColumnCount());
        }
        return $this->matrix;
    }
    
    /**
     * Advance the matrix imaginary row pointer to the next
     * row in column number $col
     *
     * @param number $col
     * @return \Gbili\Sheet\Sheet
     */
    protected function advanceMatrixColumn($col)
    {
        if (!$this->getMatrix()->canAdvanceColumn($col)) {
            $this->getMatrix()->addRow($this->getEmptySheetRow($this->getMatrixColumnCount()));
        }
        return $this->getMatrix()->advanceColumn($col);
    }
    
    /**
     * When sheet is not full return
     * Otherwise advance imagnary matrix column to next row
     * If there was an overflow, hand it to the next sheet
     *
     * @param unknown_type $lastSheetRow
     * @param unknown_type $col
     */
    protected function handleAdvancementsAndOverflows($lastSheetRow, $col)
    {
        if (!$lastSheetRow[$col] instanceof FullInterface || !$lastSheetRow[$col]->isFull()) {
            return;
        }
    
        $this->advanceMatrixColumn($col);
    
        if (!$lastSheetRow[$col]->hasOverflowLineCollection()) {
            return;
        }
        $newSheet = $this->advanceMatrixColumn($col);
        $newSheet->addLine($lastSheetRow[$col]->getOverflowLineCollection());
    }
    
    /**
     *
     * @param number $count
     * @return multitype:\Gbili\Sheet\Sheet
     */
    protected function getEmptySheetRow()
    {
        $count = $this->getMatrixColumnCount();
        $row = array();
        for ($i=0; $i < $count; $i++) {
            $row[] = $this->getEmptySheet();
        }
        return $row;
    }
    
    /**
     * Returns a sheet pertaining to the collection
     * without checking if the last created sheet
     * is full.
     *
     * @return \Gbili\Sheet\Sheet
     */
    protected function getEmptySheet()
    {
        $sheet = clone $this->getSampleSheet();
        parent::add($sheet);
        return $sheet;
    }
    
    /**
     *
     * @return \Gbili\Sheet\Sheet
     */
    public function getSampleSheet()
    {
        if (null === $this->sampleSheet) {
            $this->sampleSheet = new Sheet($this->getConfig()->getConfig());
            $this->sampleSheet->setSheetCollection($this);
        }
        return $this->sampleSheet;
    }
    
    /**
     * 
     * @return string
     */
    public function toString()
    {
        if (!$this->isFormatted) {
            $this->format();
        }
        return parent::toString();
    }
}