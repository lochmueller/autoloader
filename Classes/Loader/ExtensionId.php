<?php

/**
 * Loading eID.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\Utility\FileUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Loading eID.
 */
class ExtensionId implements LoaderInterface
{
    /**
     * Get all the complex data for the loader.
     * This return value will be cached and stored in the database
     * There is no file monitoring for this cache.
     */
    public function prepareLoader(Loader $loader, int $type): array
    {
        $scripts = [];
        $folder = ExtensionManagementUtility::extPath($loader->getExtensionKey()).'Resources/Private/Php/eID/';
        $files = FileUtility::getBaseFilesInDir($folder, 'php');

        foreach ($files as $eIdFile) {
            $scripts[] = [
                'name' => $eIdFile,
                'path' => 'EXT:'.$loader->getExtensionKey().'/Resources/Private/Php/eID/'.$eIdFile.'.php',
            ];
        }

        return $scripts;
    }

    /**
     * Run the loading process for the ext_tables.php file.
     */
    public function loadExtensionTables(Loader $loader, array $loaderInformation): void
    {
    }

    /**
     * Run the loading process for the ext_localconf.php file.
     *
     * @internal param \HDNET\Autoloader\Loader $autoLoader
     */
    public function loadExtensionConfiguration(Loader $loader, array $loaderInformation): void
    {
        foreach ($loaderInformation as $elements) {
            $GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include'][$elements['name']] = $elements['path'];
        }
    }
}
