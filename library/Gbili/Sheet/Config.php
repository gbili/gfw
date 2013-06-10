<?php
namespace Gbili\Sheet;

class Config implements ConfigInterface
{    
    private $config = array();
    
    /**
     * 
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        if (!empty($config)) {
            $this->config = $config;
        }
    }
    
    /**
     * 
     * @param string $key
     * @param boolean $value
     */
    public function overrideOption($key, $value)
    {
        if (!is_string($key)) {
            throw new Exception('overrideOption($key, $value) $key must be a string');
        }
        $this->config[$key] = $value;
    }
    
    /**
     * 
     * @param Sheet $sheet
     * @return Sheet
     */
    public function configureSheet(Sheet $sheet)
    {
        /* 
         * If true, a line will never span in a
         * sheet matrix row
         * The line will be reflown to next line
         * in same sheet (or next sheet in column)
         */
        $sheet->setReflowLongLines(
            ((!isset($this->config['reflow_long_lines']))? true : $this->config['reflow_long_lines'])
        );
        
        $sheet->setLineCountConstraint(
            ((!isset($this->config['sheet_max_lines']))?   80   : $this->config['sheet_max_lines'])
        );
        
        /*
         * If reflowLongLines is enabled, reflown
         * are added to the normal stack of lines
         * so they are accounted in lines count.
         */
        $sheet->setWidthConstraint(
            ((!isset($this->config['lines_max_length']))?  84   : $this->config['lines_max_length'])
        );
    }
}