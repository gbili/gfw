<?php
namespace Gbili\Stdlib\ThrowIf;

class Number
{
    public static function throwIfNotInRange($number, $min, $max)
    {
        Type::throwIfArgumentsWrongType(func_get_args(), 'numeric');
        //pass false, don't want to check number type twice
        self::throwIfNotLessThan($min, $number, false);
        self::throwIfNotGreaterThan($max, $number, false);
    }
    
    public static function throwIfNotLessThan($number, $than, $typeCheck=true)
    {
        if ($typeCheck) {
            Type::areArgumentsWrongType(array($number, $than), 'numeric');
        }
        
        if ((integer) $number >= (integer) $than) {
            throw new Exception("NumberError, $number is not less than $than");
        }
    }
    
    public static function throwIfNotGreaterThan($number, $than, $typeCheck=true)
    {
        if ($typeCheck) {
            Type::areArgumentsWrongType(array($number, $than), 'numeric');
        }
        
        if ((integer) $number <= (integer) $than) {
            throw new Exception("NumberError, $number is not greater than $than");
        }
    }
}