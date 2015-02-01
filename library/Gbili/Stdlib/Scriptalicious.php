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
    const RENDER_TYPE_REFERENCED='referenced';
    protected $dependencyManager;

    protected $scripts;
    protected $conditions;
    protected $lastCallIdentifier;

    /**
     * When set to true scriptalicious checks whether
     * the referenced script file exists
     *
     * @var boolean
     */
    protected $checkFileExists = false;

    /**
     * Only usefull if $checKFileExists == true
     * When set to true scriptalicious will install
     * the missing scripts in from their url
     *
     * @var boolean
     */
    protected $installMissing = false;

    protected $renderd = array();

    public function __construct()
    {
        $this->dependencyManager = new SimpleDependencyManager;
        $this->scripts = array();
        $this->scripts[self::RENDER_TYPE_INLINE] = array();
        $this->scripts[self::RENDER_TYPE_REFERENCED] = array();
    }

    public function setCheckScriptExists($bool=true)
    {
        $this->checkScriptExists = (boolean) $bool;
        return $this;
    }

    public function getCheckScriptExists()
    {
        return $this->checkScriptExists;
    }

    public function setInstallMissing($bool=true)
    {
        $this->installMissing = (boolean) $bool;
        return $this;
    }

    public function getInstallMissing()
    {
        return $this->installMissing;
    }

    public function scriptExists($src)
    {
        if (!defined(PUBLIC_DIR)) {
            throw new \Exception('You must set a constant PUBLIC_DIR, with the directory where the files should be installed');
        }
        return file_exists(PUBLIC_DIR . $src);
    }

    protected function handleMissingScript($scriptIdentifier, $script)
    {
        if ($this->getInstallMissing()) {
            throw new \Exception('TO DEVELOP: create a class to store script src to download uri and curl it into src (here $script)');
        } else {
            echo "Missing script $scriptIdentifier: $script</br>";
        }
    }

    public function addInline($scriptIdentifier, $script)
    {
        return $this->addScript($scriptIdentifier, $script, self::RENDER_TYPE_INLINE);
    }

    public function addSrc($scriptIdentifier, $script)
    {
        if ($this->getCheckScriptExists() && !$this->scriptExists($script)) {
            $this->handleMissingScript($scriptIdentifier, $script);
        }
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
            if ($this->dependencyManager->hasIdentifier($scriptIdentifier)) {
                throw new \Exception('Referenced identifier does not exist' . $scriptIdentifier);
            }
        } else if (null !== $this->lastCallIdentifier) {
            $scriptIdentifier = $this->lastCallIdentifier;
        } else {
            throw new \Exception('Unable to find the script identifier that the condition is intended for');
        }

        $this->conditions[$scriptIdentifier] = $condition;
        return $this;
    }

    public function addDependency($dependant, $dependedOn)
    {
        $this->dependencyManager->addDependency($dependant, $dependedOn);
        return $this;
    }

    /**
     * 
     */
    public function renderScriptAndDependencies($identifier)
    {
        $deps = $this->dependencyManager->getIdentifierDependencies($identifier);
        $idsToRender = $deps;
        $idsToRender[] = $identifier;
        return $this->render($idsToRender);
    }

    /**
     * Render render all the scripts contained in dependencyManager
     * in an ordered fashion
     * @return string
     */
    public function renderAll()
    {
        return $this->render($this->dependencyManager->getOrdered());
    }

    /**
     * Render render all the scripts contained in dependencyManager
     * that have not already been rendered in an ordered fashion
     * @return string
     */
    public function renderAllRest()
    {
        $all = $this->dependencyManager->getOrdered();
        $rest = array_diff($all, $this->rendered);
        return $this->render($rest);
    }

    /**
     * Render render all the scripts in $identifiers in the same order
     * @param $identifiers array 
     * @return string
     */
    public function render(array $identifiers)
    {
        $scripts = array();
        foreach ($identifiers as $identifier) {
            $this->rendered[] = $identifier;
            $scripts[] = $this->renderScript($identifier);
        }
        return implode('', $scripts);
    }

    /**
     * Render the single script identified by $identifier (no dependency resolution is made)
     * @param $identifier string
     * @return string
     */
    public function renderScript($identifier)
    {
        if (isset($this->scripts[self::RENDER_TYPE_INLINE][$identifier])) {
            $scriptHtml = $this->scripts[self::RENDER_TYPE_INLINE][$identifier];
        } else if (isset($this->scripts[self::RENDER_TYPE_REFERENCED][$identifier])) {
            $scriptHtml = '<script type="text/javascript" src="' . $this->scripts[self::RENDER_TYPE_REFERENCED][$identifier] . '"></script>';
        } else {
            throw new \Exception("Bad call to addDependency(myscript_depends_on, $identifier). The script identifier $identifier does not exist. Grep it and rename the dependency to an existing script: " . print_r($this->scripts, true) . '</br>dependencies:' . print_r($this->dependencyManager->getOrdered()));
        }
        if (isset($this->conditions[$identifier])) {
            $scriptHtml = "<!--[if {$this->conditions[$identifier]}]>$scriptHtml<![endif]-->";
        }
        return $scriptHtml;
    }
}
