<?php
namespace Gbili\Url;

use Gbili\Regex\Encapsulator\AbstractEncapsulator;

/**
 * Valid url placeholder it will also divide the url
 * in a logical placeholder way : <scheme><subdomains><authority><path>
 * <url> : http://videos.spain.megaupload.com/path?to=file/?path
 * <scheme> : http
 * <subdomains> : videos.spain
 * <authority> : megaupload.com
 * <path> : /path?to=file/?path
 * 
 * for this version only full url are allowed.
 * there must_ be a scheme and authority
 * 
 * @author gui
 *
 */
interface UrlInterface
{
    public function toString();
}
