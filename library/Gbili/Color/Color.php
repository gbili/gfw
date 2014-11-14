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
class Color
{
    const MIN = 0;
    const MAX = 255;

    const R = 'r';
    const G = 'g';
    const B = 'b';

    protected $rgbKeys = array(self::R, self::G, self::B);

    /**
     * array_combine($rgbKeys, array(255, 21, 189));
     */
    protected $rgb;

    public function __construct($param, $throwIfNotValid=false)
    {
        if (is_string($param)) {
            $rgb = $this->hexStringToHexArray($param);
        } else if (is_array($param) && (3 === count($param))) {
            if (!isset($param[self::R])) {
                $rgb = array_combine($this->rgbKeys, $param); // r=>, g=>, b=>
            } else {
                $rgb = $param;
            }
        } else if ($param instanceof Color) {
            $validRgb = $param->toDecArray();
        } else {
            throw new \Exception('Param must be array or hex color string: ' . print_r($param, true));
        }

        if (!isset($validRgb)) {
            $validRgb = array();
            foreach ($rgb as $index => $hexOrDec) {
                $validRgb[$index] = $this->between0And255($hexOrDec);
            }
        }

        if ($throwIfNotValid && isset($rgb)) {
            $argWasNotValid = array_diff($rgb, $validRgbValues);
            if (!empty($argWasNotValid)) {
                throw new \Exception('Paramter was not valid: ' . print_r($rgb, true));
            }
        }
        $this->rgb = $validRgb;
    }

    public function fraction()
    {
        if ($this->isBlack()) {
            return $this;
        }

        if ($this->isGrey()) {
            return new Color(array(1, 1, 1));
        }

        asort($this->rgb);

        foreach ($this->rgb as $lowestNotZero) {
            if (((integer) $lowestNotZero) !== 0) {
                break;
            }
        }

        $fraction = array();
        foreach ($this->rgb as $key => $value) {
            if ($value !== 0) {
                $value = ceil($value / $lowestNotZero);
            }
            $fraction[$key] = $value;
        }
        return new Color($fraction);
    }

    public function isWhite()
    {
        return $this->isEqual(new Color('FFFFFF'));
    }

    public function isBlack()
    {
        return $this->isEqual(new Color('000000'));
    }

    // 100
    // 10 50 100 -> 10 2 1
    // 5  25 50  -> 20 4 2
    // 1  5  10  -> 100 20 10
    // 
    // 100/10 20/2 10/1 -> 10 10 10
    // 100/20 20/4 10/2 -> 5  5  5
    // 
    // 20 100 100 -> 5 1 1
    public function isBetween(Color $brightest, Color $darkest, $inclusive=true)
    {
        $brightestOfTwo = $brightest->brightest($this);

        if (!$brightestOfTwo) {
            return null; //not applicable
        }
        if ($brightestOfTwo === $this) {
            return ($inclusive)
                ? $this->isEqual($brightest)
                : false;
        }
        $brightestOfTwo = $darkest->brightest($this);

        if (!$brightestOfTwo) {
            return null; //not applicable
        }

        if ($brightestOfTwo === $darkest) {
            return ($inclusive)
                ? $darkest->isEqual($this)
                : false;
        }
        return true;
    }

    public function isEqual(Color $other)
    {
        return 3 === count(array_intersect($this->rgb, $other->toDecArray()));
    }

    public function isGrey()
    {
        $lastUnit = null;
        foreach ($this->rgb as $value) {
            if (null !== $lastUnit && ($value !== $lastUnit)) return false;
            $lastUnit = $value;
        }
        return true;
    }

    public function has($what)
    {
        return in_array($what, $this->rgb);
    }

    public function brightest(Color $other)
    {
        $otherRgb = $other->toDecArray();
        $latestBrightest = null;
        foreach ($this->rgb as $k => $value) {
            //echo "this $i: {$this->rgb[$i]}, other $i: {$otherRgb[$i]} </br>";
            if ($value > $otherRgb[$k]) {
                $brightest = $this;
            } else if ($value < $otherRgb[$k]) {
                $brightest = $other;
            } else if (!isset($brightest)) {
                $brightest = null;
            }
            if (null !== $latestBrightest && $latestBrightest !== $brightest) {
                return false; //not applicable (all colors must be greater or equal in order to be applicable ex: 255 20 1 > 244 19 0)
            }
            $latestBrightest = $brightest;
        }
        return ($brightest !== null)
            ? $brightest 
            : $this; //equal
    }

    public function ratioToMax()
    {
        return $this->ratio(new Color(array(self::MAX, self::MAX, self::MAX)));
    }

    public function ratio(Color $other)
    {
        $brightest = $this->brightest($other);
        if (false === $brightest) {
            return false;
        }
        $lowest = ($brightest === $this)? $other : $this;

        $brightestRgb = $brightest->toDecArray();
        $lowestRgb   = $lowest->toDecArray();
        $ratio = array();
        foreach ($this->rgbKeys as $index) {
            $ratio[$index] = $this->decRatio($brightestRgb[$index], $lowestRgb[$index]);
        }
        return new Color($ratio);
    }

    public function decRatio($max, $min)
    {
        return ($min === 0)? $max : $max / $min;
    }

    public function hexStringToHexArray($hexColor)
    {
        if (0 === strpos($hexColor, '#')) {
            $hexColor = substr($hexColor, 1);
        }
        $len = strlen($hexColor);
        if ($len === 6) {
            $hexRgb = str_split($hexColor, 2);
        } else if ($len === 3) {
            $hexRgb = str_split($hexColor, 1);
        } else {
            throw new \Exception('String not valid as hex color: ' . print_r($hexString, true) . ', len : ' . print_r($len, true));
        }
        return array_combine($this->rgbKeys, $hexRgb);
    }

    public function red()
    {
        return $this->rgb[self::R];
    }

    public function green()
    {
        return $this->rgb[self::G];
    }

    public function blue()
    {
        return $this->rgb[self::B];
    }

    public function between0And255($hexOrDec)
    {
        $number = (is_string($hexOrDec))? hexdec($hexOrDec) : $hexOrDec;

        if (0 > $number) {
            $number = 0;
        } else if (255 < $number) {
            $number = 255;
        }
        return (integer) $number;
    }

    public function difference(Color $color, $asArray = false)
    {
        return $color->subtract($this);
    }

    public function subtract(Color $color, $asArray = false)
    {
        return $this->compute($color, function ($otherDec, $myDec) {
            return ceil($myDec) - ceil($otherDec);
        });
    }

    public function sum(Color $color, $asArray = false)
    {
        return $this->compute($color, function ($otherDec, $myDec) {
            return ceil($otherDec) + ceil($myDec);
        });
    }

    public function multiply($colorOrArray, $asArray = false)
    {
        if ($colorOrArray instanceof Color) {
            return $this->compute($colorOrArray, function ($otherDec, $myDec) {
                return ceil($otherDec) * ceil($myDec);
            });
        }

        $computedRgb = array();
        foreach ($this->rgbKeys as $index) {
            list($k, $factor) = each($colorOrArray);
            $computedRgb[] = $this->between0And255($this->rgb[$index] * $factor);
        }
        return ($asArray)
            ? $computedRgb 
            : new Color($computedRgb);
    }

    public function flip($what, $to)
    {
        $flipped = array();
        foreach ($this->rgb as $index => $value) {
            $flipped[$index] = ($value === $what)? $to : $value;
        }
        return new Color($flipped);
    }

    public function compute(Color $color, $callback, $asArray = false)
    {
        $colorRgb = $color->toDecArray();
        $computedRgb = array();
        foreach ($this->rgbKeys as $index) {
            $computedRgb[] = $this->between0And255(call_user_func_array($callback, array($colorRgb[$index], $this->rgb[$index])));
        }
        return ($asArray)
            ? $computedRgb 
            : new Color($computedRgb);
    }

    public function brightestByAverage(Color $color, $blueIsPercentDarker=20)
    {
        list($diffR, $diffG, $diffB) = $color->subtract($this, true);
        $lowerImportanceB = ($diffB * ((100 - $blueIsPercentDarker) / 100));
        $diff = $diffR + $diffG + $lowerImportanceB;
        return  ($diff > 0)
            ? $color 
            : $this;
    }

    public function darkestByAverage(Color $color, $blueIsPercentDarker=20)
    {
        return ($this->brightestByAverage($color, $blueIsPercentDarker) === $this)? $color : $this;
    }

    public function brightestByRgb($other)
    {
        $mineYours = array_combine($this->toDecArray(), $other->toDecArray());
        $greater = null;
        foreach ($mineYours as $mine => $yours) {
            $tmp = (0 < ($mine - $yours));
            if (null !== $thisgreater && $greater !== $tmp) {
                throw new \Exception('Not all subcolors (r, g, b) are greater one to one');
            }
            $greater = $tmp;
        }
        return ($greater)? $this : $other;
    }

    public function darkestByRgb(Color $color)
    {
        return ($this->brightestByRgb($color) === $this)? $color : $this;
    }

    public function toArray()
    {
        return $this->toDecArray();
    }

    public function toDecArray()
    {
        return $this->rgb;
    }

    public function toHexArray()
    {
        return array_map(function ($dec) {
            $hex = dechex($dec);
            return (2 > strlen($hex))
                ? '0' . $hex
                : $hex;
        }, $this->toDecArray());
    }

    public function toString($prepend='')
    {
        return $prepend . implode('', $this->toHexArray()); 
    }
}
