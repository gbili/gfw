<?php
namespace Gbili\Tests\Db;

class RegistryTest extends \Gbili\Tests\GbiliTestCase
{
    /**
     * Sets up the fixture, for exaple, open a network connection
     * This method is called before a test is executed
     *
     * @return void
     */
    public function setUp()
    {
        \Gbili\Db\Req\AbstractReq::setAdapter(new \StdClass);
        $prependTestsNamespace = function ($className) {
            if (is_object($className)) {
                $className = get_class($className);
            }
            foreach (array('\\Gbili', 'Gbili') as $baseNs) {
                if (false === strpos($className, $baseNs . '\\Tests')) {
                    if (false !== strpos($className, $baseNs)) {
                        $className = $baseNs . '\\Tests' . substr($className, strlen($baseNs));
                        break;
                    }
                }
            }
            //append the end of full class name
            $className = (false === strpos($className, \Gbili\Db\Registry::getClassNameEndPart()))
                ? $className . \Gbili\Db\Registry::getClassNameEndPart() 
                : $className;
            return $className;
        };
        \Gbili\Db\Registry::setReqClassNameGenerator($prependTestsNamespace);
    }

    /**
     * Tears down the fixture, for example, close a network connection
     * This method is called after a test is executed
     *
     * @return void
     */
    public function tearDown()
    {

    }

    /**
     *
     */
    public function testCanRegisterAbstractReqSubclass()
    {
        $classNameEndPart = '\\Db\\Req';
        $sameClassNameEndPart = __NAMESPACE__ . '\\Mock\\Db\\Req';
        \Gbili\Db\Registry::setClassNameEndPart($classNameEndPart);
        $instance = new $sameClassNameEndPart();
        \Gbili\Db\Registry::setInstance($instance);
        $this->assertEquals(\Gbili\Db\Registry::hasInstance($instance), true);
    }

    /**
     *
     * @expectedException \Gbili\Db\Exception
     */
    public function testCannotRegisterDifferingClassNameEndPart()
    {
        $classNameEndPart = '\\Db\\Req';
        $otherClassNameWithDifferingEndPart = __NAMESPACE__ . '\\MockAbstractReqSubclass';
        \Gbili\Db\Registry::setClassNameEndPart($classNameEndPart);
        \Gbili\Db\Registry::setInstance(new $otherClassNameWithDifferingEndPart());
    }

    /**
     *
     * @expectedException \Gbili\Db\Exception
     */
    public function testCannotRegisterNonAbstractReqSubclasses()
    {
        $someNonReqClass = new \StdClass();
        \Gbili\Db\Registry::setInstance($someNonReqClass);
    }

    public function testInstanceNotSetInRegistryAppearsAsMissing()
    {
        $obj = new \StdClass;
        $this->assertEquals(\Gbili\Db\Registry::hasInstance($obj), false);
    }

    public function testHasInstanceInRegsitry()
    {
        $obj = new Mock\Db\Req;
        \Gbili\Db\Registry::setInstance($obj);
        $this->assertEquals(\Gbili\Db\Registry::hasInstance($obj), true);
    }

    public function testReturnsSameInstance()
    {
        $obj = new Mock\Db\Req;
        \Gbili\Db\Registry::setInstance($obj);
        $this->assertEquals(\Gbili\Db\Registry::getInstance($obj), $obj);
    }
}
