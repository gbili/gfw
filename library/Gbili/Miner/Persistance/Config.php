<?php
namespace Gbili\Miner\Persistance;

use Zend\ServiceManager\ServiceManager;

use Gbili\Miner\Lexer\LexerListenerAggregate;

use Gbili\Miner\Application;

use Gbili\Miner\Application\Thread;

use Gbili\Miner\ResultsPerActionGuard;
use Gbili\Url\Authority\Host;
use Gbili\Slug\Slug;
use Gbili\Miner\Blueprint\Action\GetContents;
use Gbili\Miner\Blueprint;
use Gbili\Miner\Persistance;
use Gbili\Image\Savable as SavableImage;

class Config implements ConfigInterface
{
    const EXEC_TIME_LIMIT                         = 'exec_time_limit';
    const HTTP_REQURESTS_TIME_INTERVAL_IN_SECONDS = 'http_requests_seconds_interval';
    const REMOTE_HOST_DATA_CHARSET                = 'remote_host_data_charset';
    const SAVE_IMAGES_TO_PATH                     = 'save_images_to_path';
    
    /**
     * 
     * @var multitype
     */
    protected $config = array();
    
    /**
     * 
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->setConfig($config);
    }
    
    /**
     * (non-PHPdoc)
     * @see Gbili\Miner\Persistance.ConfigInterface::setConfig()
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }
    
    /**
     * (non-PHPdoc)
     * @see Gbili\Miner\Persistance.ConfigInterface::getConfig()
     */
    public function getConfig()
    {
        return $this->config;
    }
    
    /**
     * 
     * @param Persistance $e
     */
    public function configurePersistance(Persistance $e)
    {
        $this->setExecTimeLimit(
            !isset($this->config[self::EXEC_TIME_LIMIT])?                      86400 : $this->config[self::EXEC_TIME_LIMIT]
        );
        
        $this->setHttpRequestTimeInterval(
            !isset($this->config[self::HTTP_REQURESTS_TIME_INTERVAL_IN_SECONDS])? 20 : $this->config[self::HTTP_REQURESTS_TIME_INTERVAL_IN_SECONDS]
        );
        
        $this->setInputCharSet(
            !isset($this->config[self::REMOTE_HOST_DATA_CHARSET])?           'UTF-8' : $this->config[self::REMOTE_HOST_DATA_CHARSET]
        );
        
        if (isset($this->config[self::SAVE_IMAGES_TO_PATH])) {
            $this->setImagesPath($this->config[self::SAVE_IMAGES_TO_PATH]);
        }
    }
    
    /**
     *
     * @param unknown_type $secs
     * @return unknown_type
     */
    public function setExecTimeLimit($secs)
    {
        set_time_limit((integer) $secs);
    }
    
    /**
     *
     * @param unknown_type $secs
     * @return unknown_type
     */
    public function setHttpRequestTimeInterval($minSecs, $maxSecs = null)
    {
        GetContents::setSecondsDelayBetweenRequests($minSecs, $maxSecs);
    }
    
    /**
     * Set associated classes char set
     * @param unknown_type $str
     * @return unknown_type
     */
    public function setInputCharSet($str)
    {
        Slug::setInputCharSet(mb_strtoupper((string) $str));
    }
    
    /**
     * Mined images will be saved here
     * 
     * @param string $path
     */
    public function setImagesPath($path)
    {
        SavableImage::setPathToImagesInLFS($path);
    }
}