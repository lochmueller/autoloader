<?php
/**
 * Loading TypeConverter
 *
 * @author Tim Lochmüller
 */

namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\Utility\ClassNamingUtility;
use HDNET\Autoloader\Utility\FileUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

/**
 * Loading TypeConverter
 */
class TypeConverter implements LoaderInterface
{

    /**
     * Get all the complex data for the loader.
     * This return value will be cached and stored in the database
     * There is no file monitoring for this cache
     *
     * @param Loader $autoLoader
     * @param int    $type
     *
     * @return array
     */
    public function prepareLoader(Loader $autoLoader, $type)
    {
        $classes = [];
        $converterPath = ExtensionManagementUtility::extPath($autoLoader->getExtensionKey()) . 'Classes/Property/TypeConverter/';
        $converterClasses = FileUtility::getBaseFilesRecursivelyInDir($converterPath, 'php', true);

        foreach ($converterClasses as $converterClass) {
            $converterClass = ClassNamingUtility::getFqnByPath($autoLoader->getVendorName(), $autoLoader->getExtensionKey(),
                'Property/TypeConverter/' . $converterClass);
            if ($autoLoader->isInstantiableClass($converterClass)) {
                $classes[] = $converterClass;
            }
        }

        return $classes;
    }

    /**
     * Run the loading process for the ext_tables.php file
     *
     * @param Loader $autoLoader
     * @param array  $loaderInformation
     *
     * @return NULL
     */
    public function loadExtensionTables(Loader $autoLoader, array $loaderInformation)
    {
        return null;
    }

    /**
     * Run the loading process for the ext_localconf.php file
     *
     * @param Loader $autoLoader
     * @param array  $loaderInformation
     *
     * @return NULL
     */
    public function loadExtensionConfiguration(Loader $autoLoader, array $loaderInformation)
    {
        foreach ($loaderInformation as $class) {
            ExtensionUtility::registerTypeConverter($class);
        }
    }
}