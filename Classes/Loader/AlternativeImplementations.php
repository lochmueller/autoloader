<?php

/**
 * Loading AlternativeImplementations.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\Utility\ClassNamingUtility;
use HDNET\Autoloader\Utility\FileUtility;
use HDNET\Autoloader\Utility\ReflectionUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\Container\Container;

/**
 * Loading AlternativeImplementations.
 */
class AlternativeImplementations implements LoaderInterface
{
    /**
     * Get all the complex data for the loader.
     * This return value will be cached and stored in the database
     * There is no file monitoring for this cache.
     *
     * @return array<int, array<string, string>>
     */
    public function prepareLoader(Loader $loader, int $type): array
    {
        $classNames = [];
        $alternativeImpPath = ExtensionManagementUtility::extPath($loader->getExtensionKey()) . 'Classes/AlternativeImplementations/';
        $alternativeClasses = FileUtility::getBaseFilesInDir($alternativeImpPath, 'php');

        foreach ($alternativeClasses as $aic) {
            $aicClass = ClassNamingUtility::getFqnByPath(
                $loader->getVendorName(),
                $loader->getExtensionKey(),
                'AlternativeImplementations/' . $aic
            );

            if (!$loader->isInstantiableClass($aicClass)) {
                continue;
            }

            $classNames[] = [
                'originalName' => ReflectionUtility::getParentClassName($aicClass),
                'alternativeClassName' => $aicClass,
            ];
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
        /** @var Container $objectManager */
        $objectManager = GeneralUtility::makeInstance(Container::class);
        foreach ($loaderInformation as $classInformation) {
            $objectManager->registerImplementation($classInformation['originalName'], $classInformation['alternativeClassName']);
        }
    }
}
