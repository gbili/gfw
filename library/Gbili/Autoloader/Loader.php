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
     * Get the crazy hashed composer autoloader
     * 
     * @throws Exception
     * @return \Composer\Autoload\ClassLoader
     */
    static public function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }
        
        $loader = null;
        $autoloadPath = self::getLoaderPath();
        if ($autoloadPath !== null) {
            $loader = include $autoloadPath;
        } else {
            try {
                $loader = new \Composer\Autoload\ClassLoader();
            } catch (\Exception $e) {
                throw new \Exception("Cannot load the autoloader.php. File is unreachable, path: $autoloadPath");
            }
        }
        return self::$loader = $loader;
    }

    static public function getVendorPathWhenVendor()
    {
        return realpath(__DIR__ . '/../../../../..');
    }

    static public function getVendorPathWhenMain()
    {
        return realpath(__DIR__ . '/../../../../vendor');
    }

    static public function getLoaderPath()
    {
        $autoloadPath = self::getVendorPathWhenVendor() . '/' . 'autoload.php';
        if (file_exists($autoloadPath)) {
            return $autoloadPath;
        }
        $autoloadPath = self::getVendorPathWhenMain() . '/' . 'autoload.php';
        if (file_exists($autoloadPath)) {
            return $autoloadPath;
        }
        return null;
    }
}
