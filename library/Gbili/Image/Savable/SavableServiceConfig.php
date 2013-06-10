<?php
namespace Gbili\Image\Savable;

use Zend\ServiceManager\ConfigInterface,
    Zend\ServiceManager\ServiceLocatorInterface;

class SavableServiceConfig
implements ConfigInterface
{
    /**
     * Create db adapter service
     * You can either use the Zend\Db\Adapter\Adapter for a prefix
     * or let ReqServiceFactory create a pdo instance for a prefix
     * These two options can be mixed. Here is how:
     *
     * Expects:
     * array(
     *     'db' => array(
     *          'My\Prefix' => array(                                     //use the pdo instance from Zend\Db\Adapter\Adapter
     *              'use_zend_adapter_pdo' => 'true',
     *          ),
     *          'My\Other\Prefix' => array(                               //create another instance
     *              'username'       => 'myusername',
     *              'password'       => 'mypassword'
     *              'driver'         => 'Pdo',
     *              'dsn'            => 'mysql:dbname=miner;host=localhost',
     *              'driver_options' => array(
     *                  PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
     *              ),
     *          ),
     *  ),
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return null
     */
    public function configureServiceManager(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        
        if (isset($config['image_savable']) && isset($config['image_savable']['dir'])) {
            $dir = $config['image_savable']['dir'];
            if (!is_dir($dir)) {
                throw new Exception('The directory to save image files does not exist.');
            }
            if (!is_writable($dir)) {
                throw new Exception('The directory to save image files is not writable.');
            }
            \Gbili\Image\Savable::setPathToImagesInLFS($dir);
        } else {
            throw new Exception(__NAMESPACE__ . ' needs a key in Config array with name "image_savable" => array("dir"=>"/my/dir/name/to/save/images")');
        }
    }
}
