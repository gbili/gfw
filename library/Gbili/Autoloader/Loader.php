<?php
namespace Gbili\Autoloader;

class Loader
{
    /**
     * 
     * @var \Composer\Autoload\ClassLoader
     */
    static private $loader = null;
    
    /**
     * 
     */
    private function __construct(){}
    
    /**
     * Get the crazay hashed composer autoloader
     * 
     * @throws Exception
     * @return \Composer\Autoload\ClassLoader
     */
    static public function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }
        
        $pathToVendorsDir = realpath(__DIR__ . '/../../../../..');
        $autoloadPath = $pathToVendorsDir . '/' . 'autoload.php';
        if (!file_exists($autoloadPath)) {
             throw new Exception("Cannot load the autoloader.php. File is unreachable, path: $autoloadPath");
        }
        
        return self::$loader = include $autoloadPath;
    }
}