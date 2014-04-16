<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace GbiliViewHelper\View\Helper;

/**
 * View helper for translating messages.
 */
class Colors extends \Zend\View\Helper\AbstractHelper
{
    protected $colorFilter;

    /**
     * Translate a message
     * @return string
     */
    public function __invoke()
    {
        return $this->colorFilter;
    }

    public function setColorFilter(\GbiliViewHelper\ColorFilter $colorFilter)
    {
        $this->colorFilter = $colorFilter;
        return $this;
    }
}
