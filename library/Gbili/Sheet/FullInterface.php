<?php
namespace Gbili\Sheet;

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