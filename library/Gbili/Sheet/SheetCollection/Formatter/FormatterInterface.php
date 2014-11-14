<?php
namespace Gbili\Sheet\SheetCollection\Formatter;

use Gbili\Sheet\SheetCollection\SheetCollection;

interface FormatterInterface
{    
    /**
     * 
     * @param SheetCollection $sc
     */
    public function __construct(SheetCollection $sc);
    public function getSheetCollection();
}