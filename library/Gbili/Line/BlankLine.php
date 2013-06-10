<?php
namespace Gbili\Line;



class BlankLine extends Line
{
    /**
     *
     * @param string $content
     * @param number $index
     * @param number $maxLength
     */
    public function __construct()
    {
        parent::__construct('');
    }
}