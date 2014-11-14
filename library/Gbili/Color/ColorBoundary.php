<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Gbili\Color;

/**
 * View helper for translating messages.
 */
class ColorBoundary
{
    protected $brightestBoundary;
    protected $darkestBoundary;

    public function __construct($color1, $color2)
    {
        $this->brightestBoundary = $color1->brightestByRgb($color2);
        $this->darkestBoundary = ($this->upperBoundary === $color1)? $color1 : $color2;
    }

    static public function __callStatic($method, $params)
    {
        $twoColorsLen = strlen($method);
        if ((0 !== ($twoColorsLen % 6)) && $twoColorsLen <= 12) {
            throw new \Exception('method name length must be composed of two hex colors fff or ffffff, therefor it must be multiple of 6');
        }
        $oneColorLen = $twoColorsLen / 2;

        $colorsAsStrings = str_split($method, $oneColorLen);

        return new static(
            Color::factoryFromString($colorsAsStrings[0]), 
            Color::factoryFromString($colorsAsStrings[1])
        );
    }

    public function inBoundary($color)
    {
        return ($this->brightestBoundary->brightestByRgb($color) !== $this->brightestBoundary) 
            && ($this->darkestBoundary->darkestByRgb($color) !== $this->darkestBoundary);
    }
}
