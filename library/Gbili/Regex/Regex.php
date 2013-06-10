<?php
namespace Gbili\Regex;
/**
 * This class wraps the php preg_match and preg_match_all functions
 * It adds some commodities such as avoiding to
 * pass a $matches variable.
 * Plus it helps in retrieving the preg_match_all elements in an ordered
 * fashion with the function getNextMatch(). It remembers all the matches
 * in a preg_match_all and is able to return one match element at a time.
 * You can use getMatches() to get the whole set of matches of a preg_match_all
 * or a preg_match. If you specify an index
 * 
 * @author gui
 *
 */
class Regex
extends AbstractRegex
{
    /**
     * Allow to get a string as regex str
     * @param string $str
     * @param string|String\AbstractString $regexStr
     */
    public function __construct($str, $regexStr)
    {
        if (is_string($regexStr)) {
            $regexStr = new String\Generic($regexStr);
        }
        parent::__construct($str, $regexStr);
    }

}