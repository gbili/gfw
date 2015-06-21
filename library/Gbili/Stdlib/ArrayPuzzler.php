<?php
namespace Gbili\Stdlib;

class ArrayPuzzler
{
    /**
     * match values of $keepKeys and keys of $keepValues and for every match
     * return key => value pairs where key is key of $keepKeys and value is
     * value of $keepValues
     *
     * Ex: given $keepKeys = ['a' => (1), 2 => 'some', 'z' => ('10')]
     *         $keepValues = [(1) => 'theVal', ('10') => 'otherVal', 'sd' => 'both']
     *                return ['a' => 'theVal', 'z' => 'otherVal']
     *     () designate matches
     */
    public function puzzle(array $keepKeys, array $keepValues)
    {
        $keep = array();
        foreach ($keepKeys as $key => $keepValuesKey) {
            if (isset($keepValues[$keepValuesKey])) {
                $keep[$key] = $keepValues[$keepValuesKey];
            }
        }
        return $keep;
    }
}
