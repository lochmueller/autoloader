<?php

/**
 * Loading TypeConverter.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\Utility\ClassNamingUtility;
use HDNET\Autoloader\Utility\FileUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

/**
 * Loading TypeConverter.
 */
class TypeConverter implements LoaderInterface
{
    /**
     * Get all the complex data for the loader.
     * This return value will be cached and stored in the database
     * There is no file monitoring for this cache.
     */
    public function prepareLoader(Loader $loader, int $type): array
    {
        $classes = [];
        $converterPath = ExtensionManagementUtility::extPath($loader->getExtensionKey()).'Classes/Property/TypeConverter/';
        $converterClasses = FileUtility::getBaseFilesRecursivelyInDir($converterPath, 'php', true);

        foreach ($converterClasses as $converterClass) {
            $converterClass = ClassNamingUtility::getFqnByPath(
                $loader->getVendorName(),
                $loader->getExtensionKey(),
                'Property/TypeConverter/'.$converterClass
            );
            if ($loader->isInstantiableClass($converterClass)) {
                $classes[] = $converterClass;
            }
        }

        return $classes;
    }

    /**
     * Run the loading process for the ext_tables.php file.
     */
    public function loadExtensionTables(Loader $autoLoader, array $loaderInformation): void
    {
    }

    /**
     * Run the loading process for the ext_localconf.php file.
     */
    public function loadExtensionConfiguration(Loader $autoLoader, array $loaderInformation): void
    {
        foreach ($loaderInformation as $class) {
            ExtensionUtility::registerTypeConverter($class);
        }
    }
}
