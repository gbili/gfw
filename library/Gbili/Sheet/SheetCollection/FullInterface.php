<?php
namespace Gbili\Sheet\SheetCollection;

/**
 * 
 * @author g
 *
 */
interface FullInterface
{
    /**
     * 
     * @return boolean
     */
    public function isFull();
    
    /**
     * @return boolean
     */
    public function hasOverflowLineCollection();
    
    /**
     * 
     * @return \Gbili\Line\LineCollection\LineCollection
     */
    public function getOverflowLineCollection();
}
