<?php
/**
 * Icon loader
 *
 * @author  Tim Lochmüller
 */

namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

/**
 * Icon loader
 */
class Icon implements LoaderInterface
{

    /**
     * Get all the complex data for the loader.
     * This return value will be cached and stored in the database
     * There is no file monitoring for this cache
     *
     * @param Loader $loader
     * @param int    $type
     *
     * @return array
     */
    public function prepareLoader(Loader $loader, $type)
    {
        $icons = [];
        if (!class_exists('TYPO3\\CMS\\Core\\Imaging\\IconRegistry')) {
            return $icons;
        }

        $iconFolder = 'Resources/Public/Icon/';
        $folder = ExtensionManagementUtility::extPath($loader->getExtensionKey()) . $iconFolder;
        $extensionPath = ExtensionManagementUtility::extPath($loader->getExtensionKey());
        $files = GeneralUtility::getAllFilesAndFoldersInPath([], $folder, '', false, 0);
        if (!sizeof($files)) {
            return $icons;
        }

        foreach ($files as $path) {
            $provider = 'TYPO3\\CMS\\Core\\Imaging\\IconProvider\\BitmapIconProvider';
            if (substr(strtolower($path), -3) === 'svg') {
                $provider = 'TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider';
            }
            $relativePath = str_replace($extensionPath, '', $path);
            $iconPath = str_replace($iconFolder, '', $relativePath);

            $pathElements = PathUtility::pathinfo(strtolower(str_replace('/', '-', $iconPath)));
            $icons[] = [
                'provider'   => $provider,
                'path'       => 'EXT:' . $loader->getExtensionKey() . '/' . $relativePath,
                'identifier' => str_replace('_', '-', $loader->getExtensionKey()) . '-' . $pathElements['filename'],
            ];
        }
        return $icons;
    }

    /**
     * Run the loading process for the ext_tables.php file
     *
     * @param Loader $loader
     * @param array  $loaderInformation
     *
     * @return NULL
     */
    public function loadExtensionTables(Loader $loader, array $loaderInformation)
    {
        if (empty($loaderInformation)) {
            return;
        }

        /** @var IconRegistry $iconRegistry */
        $iconRegistry = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Imaging\\IconRegistry');

        foreach ($loaderInformation as $config) {
            $iconRegistry->registerIcon($config['identifier'], $config['provider'], ['source' => $config['path']]);
        }
        return null;
    }

    /**
     * Run the loading process for the ext_localconf.php file
     *
     * @param \HDNET\Autoloader\Loader $loader
     * @param array                    $loaderInformation
     *
     * @internal param \HDNET\Autoloader\Loader $autoLoader
     * @return NULL
     */
    public function loadExtensionConfiguration(Loader $loader, array $loaderInformation)
    {
        return null;
    }
}
