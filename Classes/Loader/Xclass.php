<?php
/**
 * Loading Xclass
 *
 * @author Tim Lochmüller
 */

namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\Utility\ClassNamingUtility;
use HDNET\Autoloader\Utility\ExtendedUtility;
use HDNET\Autoloader\Utility\FileUtility;
use HDNET\Autoloader\Utility\ReflectionUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Loading Xclass
 */
class Xclass implements LoaderInterface
{

    /**
     * Get all the complex data for the loader.
     * This return value will be cached and stored in the database
     * There is no file monitoring for this cache
     *
     * @param Loader $loader
     * @param int $type
     *
     * @return array
     */
    public function prepareLoader(Loader $loader, $type)
    {
        $return = [];
        if ($type === LoaderInterface::EXT_TABLES) {
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
     * Run the loading process for the ext_tables.php file
     *
     * @param Loader $loader
     * @param array $loaderInformation
     *
     * @return NULL
     */
    public function loadExtensionTables(Loader $loader, array $loaderInformation)
    {
        return null;
    }

    /**
     * Run the loading process for the ext_localconf.php file
     *
     * @param Loader $loader
     * @param array $loaderInformation
     *
     * @return NULL
     */
    public function loadExtensionConfiguration(Loader $loader, array $loaderInformation)
    {
        foreach ($loaderInformation as $xclass) {
            ExtendedUtility::addXclass($xclass['source'], $xclass['target']);
        }
        return null;
    }
}
