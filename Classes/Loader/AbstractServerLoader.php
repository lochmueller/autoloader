<?php

/**
 * Loading AbstractServerLoader.
 */
declare(strict_types = 1);

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
     */
    public function loadExtensionTables(Loader $autoLoader, array $loaderInformation): void
    {
        foreach ($loaderInformation as $key => $class) {
            $GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER'][$this->serverName][$key] = $class;
        }
    }

    /**
     * Run the loading process for the ext_localconf.php file.
     */
    public function loadExtensionConfiguration(Loader $autoLoader, array $loaderInformation): void
    {
        foreach ($loaderInformation as $key => $class) {
            $GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER'][$this->serverName][$key] = $class;
        }
    }
}
