<?php

/**
 * Loading SmartObjects.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\SmartObjectManager;
use HDNET\Autoloader\SmartObjectRegister;
use HDNET\Autoloader\Utility\ClassNamingUtility;
use HDNET\Autoloader\Utility\FileUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Loading SmartObjects.
 */
class SmartObjects implements LoaderInterface
{
    /**
     * Get all the complex data for the loader.
     * This return value will be cached and stored in the database
     * There is no file monitoring for this cache.
     *
     * @param Loader $loader
     * @param int    $type
     *
     * @return array
     */
    public function prepareLoader(Loader $loader, int $type): array
    {
        $configuration = [];
        $modelPath = ExtensionManagementUtility::extPath($loader->getExtensionKey()) . 'Classes/Domain/Model/';
        if (!\is_dir($modelPath)) {
            return $configuration;
        }

        $models = FileUtility::getBaseFilesRecursivelyInDir($modelPath, 'php');
        foreach ($models as $model) {
            $className = ClassNamingUtility::getFqnByPath(
                $loader->getVendorName(),
                $loader->getExtensionKey(),
                'Domain/Model/' . $model
            );
            if (SmartObjectManager::isSmartObjectClass($className)) {
                $configuration[] = $className;
            }
        }
        // already add for the following processes
        $this->addClassesToSmartRegister($configuration);

        return $configuration;
    }

    /**
     * Run the loading process for the ext_tables.php file.
     *
     * @param Loader $loader
     * @param array  $loaderInformation
     */
    public function loadExtensionTables(Loader $loader, array $loaderInformation)
    {
        $this->addClassesToSmartRegister($loaderInformation);
    }

    /**
     * Run the loading process for the ext_localconf.php file.
     *
     * @param Loader $loader
     * @param array  $loaderInformation
     */
    public function loadExtensionConfiguration(Loader $loader, array $loaderInformation)
    {
        $this->addClassesToSmartRegister($loaderInformation);
    }

    /**
     * Add the given classes to the SmartObject Register.
     *
     * @param array $loaderInformation
     */
    protected function addClassesToSmartRegister($loaderInformation)
    {
        foreach ($loaderInformation as $configuration) {
            SmartObjectRegister::register($configuration);
        }
    }
}
