<?php
namespace Gbili\File;

use Gbili\Stdlib\ToStringInterface;
use Gbili\Line\LineCollection\LineCollection;
use Gbili\Line\LineCollection\LineCollectionAwareInterface;



class File implements LineCollectionAwareInterface, ToStringInterface
{
    
    /**
     * 
     * @var string
     */
    private $fileName       = null;
    
    /**
     *
     * @var resource
     */
    private $fileHandle     = null;
    
    /**
     * 
     * @var boolean
     */
    private $eOF            = false;
    
    /**
     * 
     * @var \Gbili\Line\LineCollection\LineCollection
     */
    private $lineCollection = null;
    
    
    /**
     *
     * @param string $filename
     */
    public function __construct($filename = null)
    {
        if (null !== $filename) {
            $this->open($filename);
        }
    }
    
    /**
     * 
     * @return boolean
     */
    public function hasResource()
    {
        return (null !== $this->fileHandle) && is_resource($this->fileHandle);
    }
    
    /**
     * 
     * @param string $filename
     * @throws Exception
     */
    public function open($filename)
    {
        if ($this->hasResource()) {
            throw new Exception(" Cannot open more than one file per instance");
        }

        if (false === (is_string($filename) && is_file($filename) && is_readable($filename))){
            throw new Exception('Filename must be string, pointing to a readable filename');
        }
        
        $this->fileName   = $filename;
        
        $this->fileHandle = /*@*/fopen($this->fileName, 'rb');
        if (false === $this->fileHandle) {
            throw new Exception('Error while tying to open file: ' . $this->fileName);
        }
    }
    
    /**
     * 
     * @return boolean
     */
    public function isEOF()
    {
        return $this->eOF;
    }
    
    /**
     * 
     * @throws Exception
     * @return string
     */
    public function readLine()
    {
        if ($this->isEOF()) {
            throw new Exception("Make sure isEOF() is false before calling " . __METHOD__);
        }
        
        $fgetsReturn = fgets($this->fileHandle, 4096);
        
        if (false === $fgetsReturn) {
            return $this->eOF = true;
        }
        
        $this->getLineCollection()->add($fgetsReturn);
        return $this->eOF;
    }
    
    /**
     * 
     * @return \Gbili\File\File
     */
    public function readLines()
    {        
        while (!$this->isEOF()) {
            $this->readLine();
        }
        return $this;
    }
    
    /**
     * 
     * @return \Gbili\Line\LineCollection\LineCollection
     */
    public function getLineCollection()
    {
        if (!$this->hasLineCollection()) {
            $this->setLineCollection(new LineCollection());
        }
        
        return $this->lineCollection;
    }
    
    /**
     * 
     * @param Collection $collection
     */
    public function setLineCollection(LineCollection $lineCollection)
    {
        if (null !== $this->lineCollection) {
            throw new Exception("There is already a LineCollection for this File, cannot reset it.");
        }
        $this->lineCollection = $lineCollection;
    }
    
    /**
     * 
     * @return boolean
     */
    public function hasLineCollection()
    {
        return null !== $this->lineCollection;
    }
    
    /**
     * 
     * @return string
     */
    public function toString()
    {
        if (!$this->hasLineCollection() || $this->getLineCollection()->isEmpty()) {
            return file_get_contents($this->fileName);
        }
        return $this->getLineCollection()->toString();
    }
    
    /**
     * 
     * @return string
     */
    public function __toString()
    {
        if (!$this->hasLineCollection() || $this->getLineCollection()->isEmpty()) {
            return file_get_contents($this->fileName);
        }
        return (string) $this->getLineCollection();
    }
    
    /**
     * 
     */
    public function close()
    {
        if (is_resource($this->fileHandle)) {
            fclose($this->fileHandle);
        }
    }
    
    /**
     * 
     */
    public function __destruct()
    {
        $this->close();
    }
}