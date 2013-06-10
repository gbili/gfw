<?php
namespace Gbili\Line;

class Line
{
    private static $defaultEOLSequence = "\n";
    
    /**
     * 
     * @var string
     */
    private $content           = null;
    
    /**
     * 
     * @var string
     */
    private $eolSequence       = null;
    
    /**
     * 
     * @var integer
     */
    private $eolSequenceLength = null;
    
    /**
     * 
     * @var integer
     */
    private $maxLength         = null;
    
    /**
     * 
     * @param string $content
     * @param number $index
     * @param number $maxLength
     */
    public function __construct($content = null, $maxLength = null)
    {
        if (null !== $content) {
            $this->setContent($content);
        }
        
        if (null !== $maxLength) {
            $this->setLengthConstraint($maxLength);
        }
    }
    
    /**
     * 
     * @return string
     */
    public function getContent()
    {
        if (null === $this->content) {
            throw new Exception('No content in Line, set it before getting it');
        }
        return $this->content;
    }
    
    /**
     * 
     * @param unknown_type $chars
     * @throws Exception
     * @return \Gbili\Line
     */
    public function setContent($chars)
    {
        if (!is_string($chars)) {
            throw new Exception('setContent($param) $param must be a string');
        }
        
        $contentChars = $this->removeTrailingEOL($chars);
        
        if ($this->hasLengthConstraint() && (strlen($chars) > $this->getMaxLength())) {
            throw new Exception("The line is too long, you must split to {$this->getMaxLength()} chars long");
        }
        
        $this->content = $contentChars;
        return $this;
    }
    
    /**
     * 
     * @param string $string
     * @throws Exception
     * @return string
     */
    private function removeTrailingEOL($string)
    {
        $eolOccurencesCount = substr_count($string, $this->getEOLSequence());
        
        if (0 === $eolOccurencesCount) {
            return $string;
        }
        
        $eolExpectedRPos = -$this->getEOLSequenceLength();
        if (1 !== $eolOccurencesCount 
            || ($this->getEOLSequence() !== substr($string, $eolExpectedRPos))
        ) {
            throw new Exception('Wrong EOL sequences count, a line can contain the EOL sequence nonce or once at the very end.');
        }
        
        return substr($string, 0, $eolExpectedRPos);
    }
    
    /**
     * 
     * @return number
     */
    public function getLength()
    {
        return  $this->getContentLength() + $this->getEOLSequenceLength();
    }
    
    /**
     * 
     * @return number
     */
    public function getContentLength()
    {
        return strlen($this->getContent());
    }
    
    /**
     * 
     * @return string
     */
    public function getEOLSequence()
    {
        if (null === $this->eolSequence) {
            $this->eolSequence = self::$defaultEOLSequence;
        }
        return $this->eolSequence;
    }
    
    /**
     * 
     * @return number
     */
    public function getEOLSequenceLength()
    {
        if (null === $this->eolSequenceLength) {
            $this->eolSequenceLength = strlen($this->getEOLSequence());
        }
        return $this->eolSequenceLength;
    }
    
    /**
     * 
     * @param string $eol
     * @return \Gbili\Line
     */
    public function setEOLSequence($eol)
    {
        if (!is_string($eol)) {
            throw new Exception('setEOLSequence($para) $param must be a string');
        }
        $this->eolSequence = $eol;
        return $this;
    }
    
    /**
     * EOL included
     * 
     * @param numeric $len
     */
    public function setLengthConstraint($len)
    {
        if (!is_numeric($len)) {
            throw new Exception('setLengthConstraint($len) $len must be numeric');
        }
        $this->maxLength = (integer) $len;
        return $this;
    }
    
    /**
     * 
     * @return number
     */
    public function getLengthConstraint()
    {
        if (!$this->hasLengthConstraint()) {
            throw new Exception("You must set the length constraint before getting it");
        }
        return $this->maxLength;
    }
    
    /**
     * 
     * @return boolean
     */
    public function hasLengthConstraint()
    {
        return null !== $this->maxLength;
    }

    /**
     * 
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getContent() . $this->getEOLSequence();
    }
}