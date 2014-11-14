<?php
namespace Gbili\Db\Req;

abstract class AbstractInstaller
{
    const TABLES_SQL_FILE_RELATIVE_PATH = '/config/tables.sql';
    
    /**
     * What Gbili\Db\Req\AbstractReq prefixed adapter
     * is to be used?
     * defaults to __NAMESPACE__, which may not be registered...
     * @var string
     */
    protected $adminReqAdapterPrefix = null;
    
    /**
     * On install fail contains message
     * @var string
     */
    protected $error                 = '';
    
    /**
     * 
     * @var \Gbili\Db\Req\Admin
     */
    protected $adminReq              = null;
    
    /**
     * Tables currently in database matchin regex pattern
     * 
     * @var unknown_type
     */
    protected $existingTables        = null;
    
    /**
     * 
     * @param unknown_type $adminReqAdapterPrefix
     */
    public function __construct($adminReqAdapterPrefix = null)
    {
        if (null !== $adminReqAdapterPrefix) {
            $this->adminReqAdapterPrefix = $adminReqAdapterPrefix;
        }
    }
    
    /**
     * Create tables from config/tables.sql
     * @param boolean $deleteExisting
     * @throws Exception
     * @return boolean
     */
    public function install($deleteExisting = false)
    {
        $schemaFilePath = $this->getBaseDir() . $this->getTableSchemaPath();
        if (!file_exists($schemaFilePath)) {
            $this->error = 'Check out your miner library, '. $schemaFilePath .'is missing';
            return false;
        }
        
        $sql = file_get_contents($schemaFilePath);
        if (false === $sql) {
            $this->error = 'Could not get contents';
            return false;
        }
        
        if (false === $deleteExisting && $this->hasExistingTables()) {
            $this->error = 'The database already has tables, call install($deleteExisting = true) to overwrite them';
            return false;
        }
        
        $this->uninstall($deleteExisting);
        $this->getAdminReq()->insertUpdateData($sql);
        
        $this->resetExistingTables();
        return true;
    }
    
    /**
     * Set of table names currently existing in database 
     * that match the RegexMatchingTableNames()
     * 
     * @return Ambigous <boolean, multitype:>
     */
    public function getExistingTables()
    {
        if (null === $this->existingTables) {
            $this->existingTables = $this->getAdminReq()->getTablesFromRegex($this->getRegexMatchingTableNames());
        }
        return $this->existingTables;
    }
    
    /**
     * Call this whenever new tables are added or deleted
     */
    protected function resetExistingTables()
    {
        $this->existingTables = null;
    }
    
    /**
     * 
     * @return boolean
     */
    public function hasExistingTables()
    {
        $t = $this->getExistingTables();
        return !empty($t);
    }
    
    /**
     * Delete miner related database tables
     * So if none are related, no tables are deleted
     */
    public function uninstall($areYouSure = false)
    {
        if (!$this->hasExistingTables()) {
            return;
        }
        
        if (false === $areYouSure) {
            throw new Exception('Are you sure you want to delete tables? Call uninstall(true) if so.');
        }
        
        $this->getAdminReq()->deleteTables($this->getExistingTables());
        
        $this->resetExistingTables();
    }
    
    /**
     * 
     * @return \Gbili\Db\Req\Admin
     */
    public function getAdminReq()
    {
        if (null === $this->adminReq) {
            echo 'ADAPTER PREFIX FOR ADMIN: ' . $this->getAdapterPrefix() . "\n";
            $this->adminReq = new Admin($this->getAdapterPrefix());
        }
        return $this->adminReq;
    }
    
    /**
     * 
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }
    
    /**
     * 
     */
    public function getAdapterPrefix()
    {
        if (null === $this->adminReqAdapterPrefix) {
            $this->adminReqAdapterPrefix = $this->getInstanceNamespace(2);
        }
        return $this->adminReqAdapterPrefix;
    }
    
    /**
     * Hello/My/Classname/Is/This -> (if count = 2) -> Hello/My
     * 
     * @param number $count
     * @return string
     */
    protected function getInstanceNamespace($count)
    {
        return implode('\\', array_slice(explode('\\',get_class($this)), 0, (integer) $count));
    }
    
    /**
     * 
     * @return string
     */
    public function getTableSchemaPath()
    {
        return self::TABLES_SQL_FILE_RELATIVE_PATH;
    }
    
    /**
     * A regex string capable of matching every table name related to our classes
     * 
     * @return string
     */
    abstract public function getRegexMatchingTableNames();
    
    abstract public function getBaseDir();
}