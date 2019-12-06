<?php

/**
 * Loading Hooks.
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
 * Loading Hooks.
 */
class Hooks implements LoaderInterface
{
    /**
     * Get all the complex data for the loader.
     * This return value will be cached and stored in the database
     * There is no file monitoring for this cache.
     */
    public function prepareLoader(Loader $loader, int $type): array
    {
        $hooks = [];
        $folder = ExtensionManagementUtility::extPath($loader->getExtensionKey()) . 'Classes/Hooks/';
        $files = FileUtility::getBaseFilesInDir($folder, 'php');

        foreach ($files as $hookFile) {
            $hookClass = ClassNamingUtility::getFqnByPath(
                $loader->getVendorName(),
                $loader->getExtensionKey(),
                'Hooks/' . $hookFile
            );
            if (!$loader->isInstantiableClass($hookClass)) {
                continue;
            }

            // add class hook
            $tagConfiguration = ReflectionUtility::getTagConfigurationForClass($hookClass, ['hook']);
            if (\count($tagConfiguration['hook'])) {
                $hooks[] = [
                    'locations' => $tagConfiguration['hook'],
                    'configuration' => $hookClass,
                ];
            }

            // add method hooks
            foreach (ReflectionUtility::getPublicMethodNames($hookClass) as $methodName) {
                $tagConfiguration = ReflectionUtility::getTagConfigurationForMethod($hookClass, $methodName, ['hook']);
                if (\count($tagConfiguration['hook']) > 0) {
                    $hookLocations = \array_map(function ($hook) {
                        return \trim($hook, " \t\n\r\0\x0B|");
                    }, $tagConfiguration['hook']);

                    $hooks[] = [
                        'locations' => $hookLocations,
                        'configuration' => $hookClass . '->' . $methodName,
                    ];
                }
            }
        }

        return $hooks;
    }

    /**
     * Run the loading process for the ext_tables.php file.
     */
    public function loadExtensionTables(Loader $loader, array $loaderInformation)
    {
    }

    /**
     * Run the loading process for the ext_localconf.php file.
     *
     * @internal param \HDNET\Autoloader\Loader $autoLoader
     */
    public function loadExtensionConfiguration(Loader $loader, array $loaderInformation)
    {
        foreach ($loaderInformation as $hook) {
            ExtendedUtility::addHooks($hook['locations'], $hook['configuration']);
        }
    }
}
