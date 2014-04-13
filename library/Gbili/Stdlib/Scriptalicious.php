<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Gbili\Stdlib;

/**
 * Regsiter scripts with their dependencies
 * and render them in the right order
 */
class Scriptalicious
{
    const RENDER_TYPE_INLINE='inline';
    const RENDER_TYPE_SRC='src';
    protected $dependencyManager;

    protected $scripts;
    protected $conditions;
    protected $lastCallIdentifier;

    public function __construct()
    {
        $this->dependencyManager = new SimpleDependencyManager;
        $this->scripts = array();
        $this->scripts[self::RENDER_TYPE_INLINE] = array();
        $this->scripts[self::RENDER_TYPE_REFERENCED] = array();
    }

    public function addInline($scriptIdentifier, $script)
    {
        return $this->addScript($scriptIdentifier, $script, self::RENDER_TYPE_INLINE);
    }

    public function addSrc($scriptIdentifier, $src)
    {
        return $this->addScript($scriptIdentifier, $script, self::RENDER_TYPE_REFERENCED);
    }

    public function addScript($scriptIdentifier, $script, $renderType)
    {
        $this->dependencyManager->addIdentifier($scriptIdentifier);
        if (!isset($this->scripts[$renderType][$scriptIdentifier])) {
            $this->scripts[$renderType][$scriptIdentifier] = $script;
        }
        $this->lastCallIdentifier = $scriptIdentifier;
        return $this;
    }

    public function setCondition($condition, $scriptIdentifier=null)
    {
        if (null !== $scriptIdentifier) {
            if ($this->dependencyManager->hasIdentifier($scriptIdentifier))) {
                throw new \Exception('Referenced identifier does not exist' . $scriptIdentifier);
            }
        } else if (null !== $this->lastCallIdentifier) {
            $scriptIdentifier = $this->lastCallIdentifier;
        } else {
            throw new \Exception('Unable to find the script identifier that the condition is intended for');
        }

        $this->conditions[$scriptIdentifier] = $condition;
    }

    public function addDependency($dependant, $dependedOn)
    {
        $this->dependencyManager->addDependency($dependant, $dependedOn);
        return $this;
    }

    public function renderAll()
    {
        $scripts = array();
        foreach ($this->dependencyManager->getOrdered() as $identifier) {
            if (isset($this->scripts[self::RENDER_TYPE_INLINE][$identifier])) {
                $scriptHtml = $this->scripts[self::RENDER_TYPE_INLINE][$identifier];
            } else if (isset($this->scripts[self::RENDER_TYPE_REFERENCED][$identifier])) {
                $scriptHtml = '<script type="text/javascript" src="' . $this->scripts[self::RENDER_TYPE_REFERENCED][$identifier] . '"></script>';
            } else {
                throw new \Exception("Bad call to addDependency(myscript_depends_on, $identifier). The script identifier $identifier does not exist. Grep it and rename the dependency to an existing script");
            }
            if (isset($this->conditions[$identifier]) {
                $scriptHtml = "<!--[if {$this->conditions[$identifier]}]>$scriptHtml<![endif]-->"
            }
            $scripts[] = $scriptHtml;
        }
        return implode('', $scripts);
    }
}
