<?php
namespace Gbili\Sheet\SheetCollection\Formatter;

use Gbili\Sheet\SheetCollection\SheetCollection;
use Gbili\Sheet\Sheet;


abstract class AbstractFormatter implements FormatterInterface
{
    /**
     * 
     * @var \Gbili\Sheet\SheetCollection\SheetCollection
     */
    protected $sheetCollection;
    
    /**
     *
     * @param LineCollection $lineCollection
     */
    public function __construct(SheetCollection $sc)
    {
        $this->sheetCollection = $sc;
    }
    
    /**
     *
     * @return SheetCollection
     */
    public function getSheetCollection()
    {
        return $this->sheetCollection;
    }
    
    abstract protected function format();
}