<?php
namespace Gbili\Sheet\SheetCollection;


use Gbili\Sheet\SheetCollection\Formatter\MatrixColumnCountAwareInterface;

class Config implements ConfigInterface
{   
    /**
     * 
     * @var array
     */
    private $config = array();
    
    /**
     * 
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        if (!empty($config)) {
            $this->setConfig($config);
        }
    }
    
    /**
     * 
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }
    
    /**
     * 
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
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
     * @param Sheet $sc
     * @return Sheet
     */
    public function configureSheetCollection(SheetCollection $sc)
    {
        // Allow listeners to reset config by calling ->getConfig() ->setConfig($newConf)
        $sc->getEventManager()->trigger(__METHOD__ . '.pre', $this, $this);
        
        /*
         * Allow getting input from config, this should be moved to some service
         * locator or dependency injection thing
         */
        if (isset($this->config['input_filename'])) {
            $fileClass = (!isset($this->config['file_class']))? '\Gbili\File\File' : $this->config['file_class'];
            $file = new $fileClass($this->config['input_filename']);
            $sc->setLineCollection($file->readlines()->getLineCollection());
        }
        
        /*
         * Set the default formatter
         */
        if (isset($this->config['split_long_lines_into_matrix']) && true === $this->config['split_long_lines_into_matrix']) {
            $this->config['formatter_class'] = __NAMESPACE__ . '\Formatter\MatrixFormatter';
        } else if (!isset($this->config['formatter_class'])) {
            $this->config['formatter_class'] = __NAMESPACE__ . '\Formatter\LogFormatter';
        }
        $formatterClass = $this->config['formatter_class'];
        $sc->setFormatter(new $formatterClass($sc));
        
        /*
         * How many sheets per row?
         */
        $sc->setMatrixColumnCount(
            (!$sc->getFormatter() instanceof MatrixColumnCountAwareInterface)? 1 : $sc->getFormatter()->getMatrixColumnCount()
        );
    }
}