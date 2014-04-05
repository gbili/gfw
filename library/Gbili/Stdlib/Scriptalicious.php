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
    protected $dependencyManager;

    protected $inlineScripts;
    protected $srcScripts;

    public function __construct()
    {
        $this->dependencyManager = new SimpleDependencyManager;
    }

    public function addInline($scriptIdentifier, $script)
    {
        $this->dependencyManager->addIdentifier($scriptIdentifier);
        if (!isset($this->inlineScripts[$scriptIdentifier])) {
            $this->inlineScripts[$scriptIdentifier] = $script;
        }
        return $this;
    }

    public function addSrc($scriptIdentifier, $src)
    {
        $this->dependencyManager->addIdentifier($scriptIdentifier);
        if (!isset($this->srcScripts[$scriptIdentifier])) {
            $this->srcScripts[$scriptIdentifier] = $src;
        }
        return $this;
    }

    public function addDependency($dependant, $dependedOn)
    {
        $this->dependencyManager->addDependency($dependant, $dependedOn);
        return $this;
    }

    public function renderAll()
    {
        $scriptHtml = '';
        foreach ($this->dependencyManager->getOrdered() as $identifier) {
            if (isset($this->inlineScripts[$identifier])) {
                $scriptHtml .= $this->inlineScripts[$identifier];
            } else if (isset($this->srcScripts[$identifier])) {
                $scriptHtml .= '<script type="text/javascript" src="' . $this->srcScripts[$identifier] . '"></script>';
            } else {
                throw new \Exception("Bad call to addDependency(myscript_depends_on, $identifier). The script identifier $identifier does not exist. Grep it and rename the dependency to an existing script");
            }
        }
        return $scriptHtml;
    }
}
