<?php
namespace Gbili\Stdlib;

class Matrix
{
    protected $width = null;
    protected $height = null;
    
    protected $matrix = array();
    
    /**
     * Array where keys correspond to columns and
     * values correspond to rows.
     * 
     * @var array
     */
    protected $imaginaryRow = array();
    
    /**
     * 
     * @param number $width
     * @param number $height
     */
    public function __construct($width, $height = null)
    {
        $this->width        = (integer) $width;
        $this->imaginaryRow = array_fill(0, $this->width, 0);
        $this->height       = $height;
    }
    
    /**
     * 
     * @param integer $x
     * @param integer $y
     * @throws Exception
     * @return multitype:
     */
    public function get($x, $y)
    {
        if (!isset($this->matrix[$y][$x])) {
            throw new Exception('The requested element is not set');
        }
        return $this->matrix[$y][$x];
    }
    
    /**
     * 
     * @param array $values
     */
    public function addRow(array $values)
    {
        if ($this->isFull()) {
            throw new Exception('Out of height bounds');
        }
        
        if (count($values) > $this->width) {
            throw new Exception('Passed row is out of width bounds (too long)');
        }
        
        if (array_keys($values) !== range(0, $this->width - 1)) {
            throw new Exception('Passed row must be numerically indexed');
        }
        
        $this->matrix[] = $values;
    }
    
    /**
     * 
     * @param column $col
     * @return number
     */
    public function advanceColumn($col)
    {
        $rowK = $this->imaginaryRow[$col] + 1;
        if (!isset($this->matrix[$rowK])) {
            throw new Exception('You must addRow($values), before advanceColumn($col)');
        }
        return $this->matrix[(++$this->imaginaryRow[$col])][$col];
    }
    
    /**
     * 
     * @param number $col
     */
    public function canAdvanceColumn($col)
    {
        $rowK = $this->imaginaryRow[$col] + 1;
        return isset($this->matrix[$rowK]);
    }
    
    /**
     * Return the matrix elments that correspond to
     * those pointed by the imaginary row
     * 
     * @return multitype:
     */
    public function getImaginaryRow()
    {
        $iRow = array();
        for ($colK=0; $colK < $this->width; $colK++) {
            $rowK = $this->imaginaryRow[$colK];
            $iRow[$colK] = $this->matrix[$rowK][$colK];
        }
        return $iRow;
    }
    
    /**
     *
     * @return boolean
     */
    public function isFull()
    {
        return (null !== $this->height && $this->height > count($this->matrix));
    }
}