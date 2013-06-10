<?php
namespace Gbili\Encoding;

/**
 * 
 * @author gui
 *
 */
class Encoding
{
	/**
	 * 
	 * @param unknown_type $str
	 * @param unknown_type $convertToUtf8
	 * @return unknown_type
	 */
	public static function detectEncoding($str, $convertToUtf8 = true)
	{
		return mb_detect_encoding($str . 'a' , 'UTF-8, ISO-8859-1');
	}

	/**
	 * 
	 * @param unknown_type $anyStr
	 * @return unknown_type
	 */
	public static function utf8Encode($anyStr)
	{
		if ('UTF-8' !== self::detectEncoding($anyStr)) {
			$anyStr = utf8_encode($anyStr . 'a');
			$anyStr = mb_substr($anyStr, 0, -1);
		}
		return $anyStr;
	}
}