<?php
namespace Gbili\Stdlib;

class ArrayNestedKey
{
    public function setEl(array $keysToValue, array $haystack, $value)
    {
        return $this->applyCallback($keysToValue, $haystack, function ($key, $haystack, $value) {
            $haystack[$key] = $value;
            return $haystack;
        }, $value);
    }

    public function unsetEl(array $keysToValue, array $haystack)
    {
        return $this->applyCallback($keysToValue, $haystack, function ($key, $haystack, $otherParams) {
            unset($haystack[$key]);
            return $haystack;
        });
    }

    public function applyCallback(array $keysToValue, array $haystack, $callback, $otherParams=null)
    {
        $levelsCount = count($keysToValue);
        $current = array_shift($keysToValue);
        if ($levelsCount > 1) {
            $haystack[$current] = $this->applyCallback($keysToValue, $haystack[$current], $callback, $otherParams);
        } else {
            $haystack = $callback($current, $haystack, $otherParams);
        }
        return $haystack;
    }
}
