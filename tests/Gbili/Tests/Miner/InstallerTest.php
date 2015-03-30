<?php
namespace Gbili\Tests\Miner;

class InstallerTest extends \Gbili\Tests\GbiliTestCase
{
    /**
     * Sets up the fixture, for exaple, open a network connection
     * This method is called before a test is executed
     *
     * @return void
     */
    public function setUp()
    {
        $this->installer = new \Gbili\Miner\Installer;
        $this->installer->setTableSchemaPath(__DIR__ . '/../../../../boot/conf/db/tables_dumperengine.sql');
        return $this;
    }

    /**
     * Tears down the fixture, for example, close a network connection
     * This method is called after a test is executed
     *
     * @return void
     */
    public function tearDown()
    {
        $this->installer->uninstall(true);
    }

    public function testInstallationWorks()
    {
        $this->installer->deleteExisting(true);
        $works = $this->installer->install();
        $this->assertEquals($works, true);
    }

    public function testCanRetrieveSchemaDefinition()
    {
        $retrivesGoodFile = file_get_contents($this->installer->getTableSchemaPath()) === $this->installer->getSchemaDefinition();
        $this->assertEquals($retrivesGoodFile, true);
    }
}
