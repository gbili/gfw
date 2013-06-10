<?php
namespace Gbili\Stdlib\ThrowIf;

class ErrorMessage
{
    
    const MESSAGE_VARNAME         = 'variable';
    
    const MESSAGE_ERROR_TYPE      = 'TypeError,';
    const MESSAGE_EXPECTING       = 'must be of type:';
    const MESSAGE_PASSED          = ', currently of type:';
    
    private $testedVar    = null;
    private $expectedType = null;
    private $varName      = null;
    
    
    public function __construct()
    {
        
    }
    
    public function setVarName($varName)
    {
        $this->varName = $varName;
        return $this;
    }
    
    public function setExpectedType($expectedType)
    {
        $this->expectedType = $expectedType;
        return $this;
    }
    
    public function setTestedVar($testedVar)
    {
        $this->testedVar = $testedVar;
        return $this;
    }
    
    public function getVarName()
    {
        if (null === $this->varName) {
            $ths->varName = self::MESSAGE_VARNAME;
        }
        return $this->varName;
    }
    
    public function getExpectedType()
    {
        if (null === $this->expectedType) {
            $this->expectedType = 'expectedType is not set';
        }
        return $this->expectedType;
    }
    
    public function getTestedVar()
    {
        if (null === $this->testedVar) {
            $this->testedVar = 'testedVar is not set';
        }
        return $this->testedVar;
    }
    
    public function __toString()
    {
        $expectedType = (is_array($this->getExpectedType()))? print_r($this->getExpectedType(), true) : $this->getExpectedType();
        $msgParts = array(
            self::MESSAGE_ERROR_TYPE,
            $this->getVarName(),
            self::MESSAGE_EXPECTING,
            $expectedType,
            self::MESSAGE_PASSED,
            gettype($this->getTestedVar()),
        );
        
        return implode(' ', $msgParts);
    }
}