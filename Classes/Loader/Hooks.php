<?php

/**
 * Loading Hooks.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Loader;

use Doctrine\Common\Annotations\AnnotationReader;
use HDNET\Autoloader\Annotation\Hook;
use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\Utility\ClassNamingUtility;
use HDNET\Autoloader\Utility\ExtendedUtility;
use HDNET\Autoloader\Utility\FileUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
        $folder = ExtensionManagementUtility::extPath($loader->getExtensionKey()).'Classes/Hooks/';
        $files = FileUtility::getBaseFilesInDir($folder, 'php');

        /** @var AnnotationReader $annotationReader */
        $annotationReader = GeneralUtility::makeInstance(AnnotationReader::class);

        foreach ($files as $hookFile) {
            $hookClass = ClassNamingUtility::getFqnByPath(
                $loader->getVendorName(),
                $loader->getExtensionKey(),
                'Hooks/'.$hookFile
            );
            if (!$loader->isInstantiableClass($hookClass)) {
                continue;
            }

            $reflectionClass = new \ReflectionClass($hookClass);

            // add class hook
            $classHook = $annotationReader->getClassAnnotation($reflectionClass, Hook::class);
            if (null !== $classHook) {
                $hooks[] = [
                    'locations' => $classHook->locations,
                    'configuration' => $hookClass,
                ];
            }

            // add method hooks
            foreach ($reflectionClass->getMethods() as $method) {
                $methodHook = $annotationReader->getMethodAnnotation($method, Hook::class);
                if (null !== $methodHook) {
                    $hooks[] = [
                        'locations' => $methodHook->locations,
                        'configuration' => $hookClass.'->'.$method->getName(),
                    ];
                }
            }
        }

        return $hooks;
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
        foreach ($loaderInformation as $hook) {
            ExtendedUtility::addHooks($hook['locations'], $hook['configuration']);
        }
    }
}
