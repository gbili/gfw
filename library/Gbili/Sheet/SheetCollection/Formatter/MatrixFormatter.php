<?php
namespace Gbili\Sheet\SheetCollection\Formatter;

use Gbili\Sheet\SheetCollection\Formatter\AbstractFormatter;

use Gbili\Line\BlankLine;
use Gbili\Sheet\Sheet;
use Gbili\Line\Line;
use Gbili\Sheet\Config                              as SheetConfig;
use Gbili\Sheet\SheetCollection\SheetCollection;
use Gbili\Line\LineCollection\LineCollection;

class MatrixFormatter extends AbstractFormatter implements MatrixColumnCountAwareInterface
{   
    /**
     * 
     * @var \Gbili\Sheet\Sheet
     */
    protected $sampleSheet = null;
    
    /**
     * 
     * @var \Gbili\Line\Line
     */
    protected $longestLine = null;
    
    /**
     * Sheet Width Constraint - line EOLSequence lenght
     * @var number
     */
    protected $allowedLineContentLengthGivenSheetWidthConstraint = null;
    
    /**
     * 
     * @var \Gbili\Sheet\Sheet
     */
    protected $lineSplitterSheet = null;
    
    /**
     *
     * @return \Gbili\Sheet\Sheet
     */
    public function getSampleSheet()
    {
        if (null === $this->sampleSheet) {
            $this->sampleSheet = $this->getSheetCollection()->getSampleSheet();
            if (!$this->sampleSheet->hasWidthConstraint()) {
                throw new Exception("You don't need, thus you are not allowed to use a matrix without width constraint, use LogFormatter instead");
            }
        }
        return $this->sampleSheet;
    }
    
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
        foreach ($this->getSheetCollection()->getLineCollection() as $line) {
            $this->splitLineAcrossColumns($line);
        }
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
        $sheetCollection = $this->getSheetCollection()->getSheetRow();
        
        $hasLineSplits = $lineSplitsCollection->count() > 0;

        if ($lineSplitsCollection->count() !== count($sheetCollection)) {
            throw new Exception("CountMismatch, there should be as many splits as sheet columns");
        }
        
        /*reset($sheetCollection);
        $lineSplitsCollection->rewind();*/
        
        foreach ($sheetCollection as $sheet) {
            if (false === $hasLineSplits) {
                throw new Exception("There are more line splits than sheets in row");
            }

            if ($sheet->isFull()) {
                throw new Exception("The sheet is full cannot add more lines to it @or maybe yes, if using overflow?");
            }
            
            $sheet->addLine($lineSplitsCollection->current());
            $hasLineSplits = $lineSplitsCollection->getNext();
        }
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
                'lines_max_length'  => $this->getSampleSheet()->getWidthConstraint(),
                'sheet_max_lines'   => $everyLineMustBeSplitOrEnlargedIntoCount
            );
            $this->lineSplitterSheet = new Sheet($config);
        }
        return clone $this->lineSplitterSheet;
    }
    
    /**
     * 
     * @return \Gbili\Line\Line
     */
    protected function getLongestLine()
    {
        if (null === $this->longestLine) {
            $this->longestLine = $this->getSheetCollection()->getLineCollection()->getLongest();
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
    public function getMatrixColumnCount()
    {
        if (0 >= $this->getAllowedLineContentLengthGivenSheetWidthConstraint()) {
            throw new Exception("Cannot divide by zero, and dont want to divide by negative");
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
                $this->getSampleSheet()->getWidthConstraint() - $this->getLongestLine()->getEOLSequenceLength();
        }
        return $this->allowedLineContentLengthGivenSheetWidthConstraint;
    }
}