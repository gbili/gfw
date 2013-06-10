<?php
namespace Gbili\Line;



class IndexedBlankLine extends BlankLine
{
    /**
     *
     * @param string $content
     * @param number $index
     * @param number $maxLength
     */
    public function __construct($index = null)
    {
        parent::__construct('');
    }
}