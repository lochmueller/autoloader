<?php
/**
 * Loading AbstractServerLoader.
 */
namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;

/**
 * Loading AbstractServerLoader.
 */
abstract class AbstractServerLoader implements LoaderInterface
{
    /**
     * Server name.
     *
     * @var string
     */
    protected $serverName = 'unknown';

    /**
     * Run the loading process for the ext_tables.php file.
     *
     * @param Loader $autoLoader
     * @param array  $loaderInformation
     */
    public function loadExtensionTables(Loader $autoLoader, array $loaderInformation)
    {
        foreach ($loaderInformation as $key => $class) {
            $GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER'][$this->serverName][$key] = $class;
        }

        return null;
    }

    /**
     * Run the loading process for the ext_localconf.php file.
     *
     * @param Loader $autoLoader
     * @param array  $loaderInformation
     */
    public function loadExtensionConfiguration(Loader $autoLoader, array $loaderInformation)
    {
        foreach ($loaderInformation as $key => $class) {
            $GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER'][$this->serverName][$key] = $class;
        }

        return null;
    }
}
