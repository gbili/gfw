<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace GbiliViewHelper;

/**
 * View helper for translating messages.
 */
class ColorFilter
{
    public function compute($hexColor, $callback, $params)
    {
        $color = new Color($hexColor);
        $computedRgb = array();
        foreach ($color->toDecArray() as $index => $value) {
            $callbackParams = (!is_array($params))
                ? array($params)
                : $params;
            array_unshift($callbackParams, $value);
            $computedRgb[] = call_user_func_array($callback, $callbackParams);
        }
        return new Color($computedRgb);
    }

    public function getWhiterColorsByFractions($hexColor, $stepFractions=1)
    {
        $color = new Color($hexColor);
        $whiterColors = array();
        while (!$color->isWhite()) {
            $newColor = $this->brightenByFractions($color->toString(), $stepFractions);
            $whiterColors[] = $newColor;
            $color = $newColor;
        }
        return $whiterColors;
    }

    public function getWhiterColorsByPercent($hexColor, $percent=10)
    {
        $color = new Color($hexColor);
        $whiterColors = array();
        $joker = new Color(array(10,10,10));
        if ($color->has(0)) {
            $color = $color->sum($joker); // avoid trying to add n percent of 0
        }
        while (!$color->isWhite()) {
            $newColor = $this->brightenByPercent($color->toString(), $percent);
            if ($newColor->isEqual($color)) {
                $newColor = $newColor->sum($joker);
            } else if (($added = $newColor->subtract($color)) && $added->has(0)) {
                $newColor = $color->sum($added->flip(0, 1));// avoid trying to add n percent of 0
            }
            $whiterColors[] = $newColor;
            $color = $newColor;
        }
        return $whiterColors;
    }

    public function getWhiterColorsByHex($hexColor, $units=1)
    {
        $color = new Color($hexColor);
        $whiterColors = array();
        $increment = new Color(array_fill(0, 3, $units));
        while (!$color->isWhite()) {
            $newColor = $color->sum($increment);
            $whiterColors[] = $newColor;
            $color = $newColor;
        }
        return $whiterColors;
    }

    public function getDarkerColorsByFractions($hexColor, $stepFractions=1)
    {
        $color = new Color($hexColor);
        $darkerColors = array();
        while (!$color->isBlack()) {
            $newColor = $this->darkenByFractions($color->toString(), $stepFractions);
            $darkerColors[] = $newColor;
            $color = $newColor;
        }
        return $darkerColors;
    }

    public function getDarkerColorsByPercent($hexColor, $percent=10)
    {
        $color = new Color($hexColor);
        $darkerColors = array();
        $i=0;
        while (!$color->isBlack()) {
            $newColor = $this->darkenByPercent($color->toString(), $percent);
            $darkerColors[] = $newColor;
            $color = $newColor;
        }
        return $darkerColors;
    }

    public function getDarkerColorsByHex($hexColor, $units=1)
    {
        $color = new Color($hexColor);
        $darkerColors = array();
        $increment = new Color(array_fill(0, 3, $units));
        while (!$color->isBlack()) {
            $newColor = $color->subtract($increment);
            $darkerColors[] = $newColor;
            $color = $newColor;
        }
        return $darkerColors;
    }

    public function darkenByPercent($hexColor, $percent)
    {
        return $this->applyPercent($hexColor, $percent, $add=false);
    }

    public function brightenByFractions($hexColor, $increaseHighestByFractions=1)
    {
        $color = new Color($hexColor);
        if ($color->isWhite()) {
            return $color;
        }

        if ($color->isGrey()) {
            $inc = $increaseHighestByFractions;
            return $color->sum(new Color(array($inc, $inc, $inc)));
        }

        $white = new Color('FFFFFF');
        for ($i=0;($i<255) && ($i<$increaseHighestByFractions) && !$color->isWhite();$i++) {
            $diff = $color->difference($white);
            $fraction = $diff->fraction();
            $color = $color->sum($fraction);
        }
        return $color;
    }

    protected function applyPercent($hexColor, $percent, $add=true)
    {
        $color = new Color($hexColor);
        if ($color->isWhite()) {
            return $color;
        }
        $multiply = ($add)
            ? 1 + ($percent / 100)
            : 1 - ($percent / 100);
        return $color->multiply(array_fill(0,3,$multiply));
    }

    public function brightenByPercent($hexColor, $percent)
    {
        return $this->applyPercent($hexColor, $percent, $add=true);
    }

    public function brightenByDarkestNTimes($hexColor, $times)
    {
        $color = new Color($hexColor);
        if ($color->isWhite()) {
            return $color;
        }

        if ($color->isGrey()) {
            $inc = $increaseHighestByFractions;
            return $color->sum(new Color(array($inc, $inc, $inc)));
        }

        $white = new Color('FFFFFF');
        for ($i=0;($i<255) && ($i<$increaseHighestByFractions) && !$color->isWhite();$i++) {
            $diff = $color->difference($white);
            $fraction = $diff->fraction();
            $color = $color->sum($fraction);
        }
        return $color;
    }

    public function darkenByFractions($hexColor, $decreaseHighestByFractions=1)
    {
        $color = new Color($hexColor);
        if ($color->isBlack()) {
            return $color;
        }

        if ($color->isGrey()) {
            $inc = $decreaseHighestByFractions;
            return $color->difference(new Color(array($inc, $inc, $inc)));
        }

        $fraction = $color->fraction();

        for ($i=0;($i<255) && ($i<$decreaseHighestByFractions) && !$color->isBlack();$i++) {
            $color = $color->subtract($fraction);
        }
        return $color;
    }

    public function decreaseByPercent($amount, $percent)
    {
        $decreaseBy = $this->percentOf($percent, $amount);
        return $amount - $decreaseBy;
    }

    public function increaseByPercent($amount, $percent)
    {
        $increaseBy = $this->percentOf($percent, $amount);
        if (1 > $increaseBy) {
            $increaseBy = 1;
        }
        return $amount + $increaseBy;
    }

    public function percentOf($percent, $amount)
    {
        return floor(($percent / 100) * $amount);
    }

    public function oposite($number, $onAverageBrighten=false)
    {
        $bright                      = 255;
        $dark                        = 0;
        $differenceToConsiderOposite = 127;

        $differenceWithTop           = 255 - $number;
        $differenceWithNumber = abs(($differenceWithTop - $number));

        if ($differenceToConsiderOposite > $differenceWithNumber) {
            if ($number > $differenceToConsiderOposite) {
                return $number - $differenceToConsiderOposite;
            } else {
                return $number + $differenceToConsiderOposite;
            }
            return ($onAverageBrighten)? $bright : $dark;
        }
        return $differenceWithTop;
    }

    public function readableTextover($hexColor, $onAverageBrighten=false)
    {
        return $this->compute($hexColor, array($this, 'oposite'), array($onAverageBrighten));
    }
}
