<?php

/**
 * Loading Xclass.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\Utility\ClassNamingUtility;
use HDNET\Autoloader\Utility\ExtendedUtility;
use HDNET\Autoloader\Utility\FileUtility;
use HDNET\Autoloader\Utility\ReflectionUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Loading Xclass.
 */
class Xclass implements LoaderInterface
{
    /**
     * Get all the complex data for the loader.
     * This return value will be cached and stored in the database
     * There is no file monitoring for this cache.
     *
     * @return mixed[]
     */
    public function prepareLoader(Loader $loader, int $type): array
    {
        $return = [];
        if (LoaderInterface::EXT_TABLES === $type) {
            return $return;
        }
        $xClassesPath = ExtensionManagementUtility::extPath($loader->getExtensionKey()) . 'Classes/Xclass/';
        $xClasses = FileUtility::getBaseFilesRecursivelyInDir($xClassesPath, 'php');

        foreach ($xClasses as $xClass) {
            $className = ClassNamingUtility::getFqnByPath(
                $loader->getVendorName(),
                $loader->getExtensionKey(),
                'Xclass/' . $xClass
            );
            if (!$loader->isInstantiableClass($className)) {
                continue;
            }

            $return[] = [
                'source' => ReflectionUtility::getParentClassName($className),
                'target' => $className,
            ];
        }

        return $return;
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
        foreach ($loaderInformation as $xclass) {
            ExtendedUtility::addXclass($xclass['source'], $xclass['target']);
        }
    }
}
