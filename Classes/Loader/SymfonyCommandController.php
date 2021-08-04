<?php

/**
 * SymfonyCommandController.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\Utility\ClassNamingUtility;
use HDNET\Autoloader\Utility\FileUtility;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * SymfonyCommandController.
 */
class SymfonyCommandController implements LoaderInterface
{
    /**
     * Get all the complex data and information for the loader.
     * This return value will be cached and stored in the core_cache of TYPO3.
     * There is no file monitoring for this cache.
     *
     * @see https://docs.typo3.org/typo3cms/InsideTypo3Reference/CoreArchitecture/BackendModules/CliScripts/Index.html
     */
    public function prepareLoader(Loader $loader, int $type): array
    {
        $classNames = [];
        $commandConfigurationFile = ExtensionManagementUtility::extPath($loader->getExtensionKey()).'Configuration/Commands.php';

        if (is_file($commandConfigurationFile)) {
            return [];
        }

        $commandPath = ExtensionManagementUtility::extPath($loader->getExtensionKey()).'Classes/Command/';
        $controllers = FileUtility::getBaseFilesInDir($commandPath, 'php');
        foreach ($controllers as $controller) {
            $className = ClassNamingUtility::getFqnByPath(
                $loader->getVendorName(),
                $loader->getExtensionKey(),
                'Command/'.$controller
            );
            if (!$loader->isInstantiableClass($className)) {
                continue;
            }

            if (is_subclass_of($className, \Symfony\Component\Console\Command\Command::class)) {
                $classNames[lcfirst($controller)] = $className;
            }
        }

        if (empty($classNames)) {
            return [];
        }

        $configuration = [];
        foreach ($classNames as $name => $class) {
            $configuration[$loader->getExtensionKey().':'.$name] = [
                'class' => $class,
            ];
        }

        $content = '<?php
// This file is geenrated by EXT:autoloader. If you delete this file, clear the System cache and autoloader will generate a new one ;)

return '.ArrayUtility::arrayExport($configuration).';';

        FileUtility::writeFileAndCreateFolder($commandConfigurationFile, $content);

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
    }
}
