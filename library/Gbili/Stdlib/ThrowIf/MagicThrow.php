<?php
namespace Gbili\Stdlib\ThrowIf;

class MagicThrow
{
    /**
     * Call like this : self::is_array($var)  //throws because $var is expected to be an array
     *                  self::not_is_array($var) //throws because $var should be an array
     *                  self::throwIfNotNumeric($varToTest)
     *                  etc.
     *
     * @param unknown_type $calledMethod
     * @param array $arguments
     *     0 => variable to test,
     *     1 => optional, variable to test name
     * @throws Exception
     */
    public static function __callStatic($callFunction, $arguments)
    {
        if (empty($arguments)) {
            throw new Exception('You must pass some variable to test to : ' . $callFunction);
        }

        if ($negate = (0 === strpos($callFunction, 'not_'))) {
            $callFunction = substr($callFunction, 4);
        }

        if (function_exists($callFunction)) {
            $boolResult = (boolean) call_user_func_array($callFunction, $arguments);
        } else if ('is_true' === $callFunction || 'is_false' === $callFunction) {
            self::not_is_bool($arg0 = current($arguments));
            echo $callFunction . ' is:'; var_dump($arg0);
            $boolResult = ('is_true' === $callFunction) === $arg0;
        } else {
            throw new Exception('The called function does not match any existing function');
        }

        if ((!$negate && $boolResult) || ($negate && !$boolResult)) {
            throw new Exception($callFunction . '($arguments) did' . (($negate)? ' not': '') . ' return a' . (($boolResult)? ' true': ' false') . ' value, so $arguments matched the test');
        }
    }
}