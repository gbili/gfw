<?php
namespace Gbili\Sheet\SheetCollection\Formatter;

use Gbili\Sheet\SheetCollection\Formatter\AbstractFormatter;

use Gbili\Line\BlankLine;



use Gbili\Sheet\Sheet;
use Gbili\Line\Line;
use Gbili\Sheet\Config                              as SheetConfig;
use Gbili\Sheet\SheetCollection\SheetCollection;
use Gbili\Line\LineCollection\LineCollection;

class CopyOfMatrixFormatter extends AbstractFormatter implements RowSheetCountFormatterInterface
{
    
    /**
     * 
     * @var \Gbili\Line\Line
     */
    private $longestLine = null;
    
    /**
     * 
     * @var \Gbili\Sheet\SheetCollection\SheetCollection
     */
    private $currentSheetRow = null;
    
    /**
     * 
     * @var \Gbili\Sheet\SheetCollection\SheetCollection
     */
    private $sampleSheetRow = null;
    
    /**
     * Sheet Width Constraint - line EOLSequence lenght
     * @var number
     */
    private $allowedLineContentLengthGivenSheetWidthConstraint = null;
    
    /**
     * 
     * @var \Gbili\Sheet\Sheet
     */
    private $lineSplitterSheet = null;
    
    /**
     * @todo line collection should handle the split 
     * of too long lines into a lineCollection?
     * So should add Config with line max length to 
     * lineCollection and make sure it does not 
     * conflic with sheet max width
     * 
     * With this type of sheet collection,
     * sheets must have a width constraint
     */
    public function format()
    {
        $sampleSheet = $this->getGenerator()->getSampleSheet();
        if (!$sampleSheet->hasWidthConstraint()) {
            throw new Exception("Complete");
        }
        var_dump($this->getGenerator()->getLineCollection());
        /*foreach ($this->getGenerator()->getLineCollection() as $line) {
            $this->splitLineAcrossColumns($line);
        }*/
    }
    
    /**
     * 
     * @param Line $line
     */
    protected function splitLineAcrossColumns(Line $line)
    {
        $lc = $this->splitLineIntoShorterLineChunksCollection($line);
        $this->addLineSplitsToMatchingSheetColumnsInCurrentSheetRow($lc);
    }
    
    /**
     * Sheets have the ability to split long lines
     * into shorter chunks such that they fit the
     * sheet width. We use this feature to create a
     * collection of lines with a certain width from
     * a longer line.
     * If the line is not long enough the remaining
     * lines will be filled with eol until required 
     * row of sheet collection count 
     * 
     * @param Line $line
     * @return LineCollection;
     */
    public function splitLineIntoShorterLineChunksCollection(Line $line)
    {
        $lineSplitter = $this->getLineSplitterSheetClone();
        $lineSplitter->addLine($line);
        return  $lineSplitter->getLineCollection();
    }
    
    /**
     * Each line split is added to the corresponding
     * sheet inside the current row that is not full
     * 
     * @param LineCollection $lineSplitsCollection
     */
    protected function addLineSplitsToMatchingSheetColumnsInCurrentSheetRow(LineCollection $lineSplitsCollection)
    {
        $sheetCollection = $this->getCurrentSheetRow();
        
        $hasLineSplits = $lineSplitsCollection->count() > 0;
        $hasSheets     = $sheetCollection->count() > 0;
        
        if ($lineSplitsCollection->count() !== $sheetCollection->count()) {
            throw new Exception('CountMismatch, line splits collection must have the same count of lines than there are sheets in row');
        }
        
        $sheetCollection->rewind();
        $lineSplitsCollection->rewind();
        
        echo ' COUNTING::' .  $lineSplitsCollection->count();
        
        /*for ($i = 7; $i > 0; $i--) {
            if (false === $hasSheets || false === $hasLineSplits) {
                throw new Exception(" @throwif There are more line splits than sheets in row");
            }

            var_dump($sheetCollection->current());

            if ($sheetCollection->current()->isFull()) {
                throw new Exception("This exception text was generated automatically, please adapt it");
            }

            
            $sheetCollection->current()->addLine($lineSplitsCollection->current());
            
            $hasLineSplits = $lineSplitsCollection->getNext();
            $hasSheets = $sheetCollection->getNext();
        }*/
    }
    
    /**
     * Use the sheet capacity to fill line collection up to
     * sheet max lines,
     * Use the sheet capacity to reflow long lines
     * into shorter lines,
     * And copy our sample sheet width constraint to get lines
     * of the right width
     * 
     * @return \Gbili\Sheet\Sheet
     */
    protected function getLineSplitterSheetClone()
    {
        if (null === $this->lineSplitterSheet) {
            $everyLineMustBeSplitOrEnlargedIntoCount = $this->getMatrixColumnCount();
            $config = array(
                'reflow_long_lines' => true,
                'lines_max_length'  => $this->getGenerator()->getSampleSheet()->getWidthConstraint(),
                'sheet_max_lines'   => $everyLineMustBeSplitOrEnlargedIntoCount
            );
            $this->lineSplitterSheet = new Sheet($config);
        }
        return clone $this->lineSplitterSheet;
    }
    
    /**
     * If the current sheet row is full, create a new row
     * and set it as current sheet row
     * 
     * @return \Gbili\Sheet\SheetCollection\SheetCollection
     */
    protected function getCurrentSheetRow()
    {
        /*if (null === $this->currentSheetRow) {
            return $this->currentSheetRow = $this->cloneSampleSheetRow();
        }
        
        if ($this->currentSheetRow->isEmpty() || !$this->currentSheetRow->getSample()->isFull()) {
            return $this->currentSheetRow;
        }
        
        $this->addElementsToOutputCollection($this->currentSheetRow);
        $this->currentSheetRow = null;
        
        return $this->getCurrentSheetRow();*/
    }
    
    /**
     * Create a collection of sheets that represent a row
     * of columnCount number of Sheets
     * 
     * @return \Gbili\Sheet\SheetCollection\SheetCollection
     */
    protected function cloneSampleSheetRow()
    {
        if (null === $this->sampleSheetRow) {
            $this->initSampleSheetRow();
        }
        return clone $this->sampleSheetRow;
    }
    
    /**
     * Init as a sheet collection of count row columns count
     * 
     */
    private function initSampleSheetRow()
    {
        $this->sampleSheetRow = new SheetCollection();
        $columnCount = $this->getMatrixColumnCount();
        /*for ($i = 0; $i < $columnCount; $i++) {
            $this->sampleSheetRow->add($this->getGenerator()->cloneSampleSheet());
        }*/
    }
    
    /**
     * 
     * @return \Gbili\Line\Line
     */
    protected function getLongestLine()
    {
        if (null === $this->longestLine) {
            $this->longestLine = $this->getGenerator()->getLineCollection()->getLongest();
        }
        return $this->longestLine;
    }
    
    /**
     * How many columns does a row of sheet contain
     * or how many sheets does a sheet row contain.
     * 
     * @param Sheet $sheet
     * @return number
     */
    protected function getMatrixColumnCount()
    {
        if (0 >= $this->getAllowedLineContentLengthGivenSheetWidthConstraint()) {
            throw new Exception('Cannot divide by zero');
        }
        return  (int) ceil( $this->getLongestLine()->getContentLength() / $this->getAllowedLineContentLengthGivenSheetWidthConstraint());
    }
    
    /**
     *
     * @return number
     */
    protected function getAllowedLineContentLengthGivenSheetWidthConstraint()
    {
        if (null === $this->allowedLineContentLengthGivenSheetWidthConstraint) {
            $this->allowedLineContentLengthGivenSheetWidthConstraint =
                $this->getGenerator()->getSampleSheet()->getWidthConstraint() - $this->getLongestLine()->getEOLSequenceLength();
        }
        echo 'get allowed:'; var_dump($this->allowedLineContentLengthGivenSheetWidthConstraint);
        return $this->allowedLineContentLengthGivenSheetWidthConstraint;
    }
}
