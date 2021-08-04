<?php

/**
 * Loading CommandController.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\Utility\ClassNamingUtility;
use HDNET\Autoloader\Utility\FileUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Loading CommandController.
 */
class CommandController implements LoaderInterface
{
    /**
     * Get all the complex data for the loader.
     * This return value will be cached and stored in the database
     * There is no file monitoring for this cache.
     */
    public function prepareLoader(Loader $loader, int $type): array
    {
        $classNames = [];
        $commandPath = ExtensionManagementUtility::extPath($loader->getExtensionKey()).'Classes/Command/';
        $controllers = FileUtility::getBaseFilesInDir($commandPath, 'php');
        foreach ($controllers as $controller) {
            if ('AbstractCommandController' === $controller) {
                continue;
            }

            $className = ClassNamingUtility::getFqnByPath(
                $loader->getVendorName(),
                $loader->getExtensionKey(),
                'Command/'.$controller
            );
            if (!$loader->isInstantiableClass($className)) {
                continue;
            }

            $classNames[] = $className;
        }

        return $classNames;
    }

    /**
     * Run the loading process for the ext_tables.php file.
     */
    public function loadExtensionTables(Loader $loader, array $loaderInformation): void
    {
    }

    /**
     * Run the loading process for the ext_localconf.php file.
     */
    public function loadExtensionConfiguration(Loader $loader, array $loaderInformation): void
    {
        foreach ($loaderInformation as $className) {
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = $className;
        }
    }
}
