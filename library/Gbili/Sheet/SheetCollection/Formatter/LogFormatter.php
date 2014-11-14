<?php
namespace Gbili\Sheet\SheetCollection\Formatter;

class LogFormatter extends AbstractFormatter
{   
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
            $this->getSheetCollection()->getSheet()->addLine($line);
        }
    }
}