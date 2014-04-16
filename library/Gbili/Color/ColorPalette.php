<?php
namespace Gbili\Color;

class ColorPalette
{
    protected $baseColor;

    public function __construct(Color $color)
    {
        $this->baseColor = $color;
    }

    public function drawHex()
    {
        $colorFilter = new \Gbili\Color\ColorFilter();
        $drawing = '<ul style="width:750px;">';
        $darkers = $colorFilter->getDarkerColorsByHex($this->baseColor, 10);
        $totalWidth = 700;
        foreach ($darkers as $darker) {
            $drawing .= '<li style="">';
            $drawing .= "<div style=\"background-color:#{$darker->toString()};height:30px;width:3px;display:block;float:left;\"></div>";
            $whiters = $colorFilter->getWhiterColorsByHex($darker, 10);
            $wcount = count($whiters);
            $width = (integer) $totalWidth / $wcount;
            foreach ($whiters as $whiter) {
                $drawing .= "<div style=\"background-color:#{$whiter->toString()};height:30px;width:{$width}px;display:block;float:left;\"></div>";
            }
            $drawing .= '</li>';
        }
        $drawing .= '</ul>';

        return $drawing;
    }

    public function drawFractions()
    {
        $colorFilter = new \Gbili\Color\ColorFilter();
        $drawing = '<ul style="overflow:scroll;">';
        $darkers = $colorFilter->getDarkerColorsByFractions($this->baseColor);
        $totalWidth = 700;
        foreach ($darkers as $darker) {
            $drawing .= '<li style="">';
            $drawing .= "<div style=\"background-color:#{$darker->toString()};height:30px;width:3px;display:block;float:left;\"></div>";
            $whiters = $colorFilter->getWhiterColorsByFractions($darker);
            $wcount = count($whiters);
            $width = (integer) $totalWidth / $wcount;
            foreach ($whiters as $whiter) {
                $drawing .= "<div style=\"background-color:#{$whiter->toString()};height:30px;width:{$width}px;display:block;float:left;\"></div>";
            }
            $drawing .= '</li>';
        }
        $drawing .= '</ul>';

        return $drawing;
    }

    public function drawPercents()
    {
        $colorFilter = new \Gbili\Color\ColorFilter();
        $drawing = '<ul style="overflow:scroll;">';
        $darkers = $colorFilter->getDarkerColorsByPercent($this->baseColor);
        $totalWidth = 700;
        foreach ($darkers as $darker) {
            $drawing .= '<li style="">';
            $drawing .= "<div style=\"background-color:#{$darker->toString()};height:30px;width:3px;display:block;float:left;\"></div>";
            $whiters = $colorFilter->getWhiterColorsByPercent($darker);
            $wcount = count($whiters);
            $width = (integer) $totalWidth / $wcount;
            foreach ($whiters as $whiter) {
                $drawing .= "<div style=\"background-color:#{$whiter->toString()};height:30px;width:{$width}px;display:block;float:left;\"></div>";
            }
            $drawing .= '</li>';
        }
        $drawing .= '</ul>';

        return $drawing;
    }
}
