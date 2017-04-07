<?php
/**
 * Icon loader
 *
 * @author  Tim Lochmüller
 */

namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
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
     * @param int $type
     *
     * @return array
     */
    public function prepareLoader(Loader $loader, $type)
    {
        $icons = [];
        if (!class_exists(IconRegistry::class)) {
            return $icons;
        }

        return array_merge($this->getIconsByPath($loader, 'Resources/Public/Icon/'), $this->getIconsByPath($loader, 'Resources/Public/Icons/'));
    }

    /**
     * Get the icons
     *
     * @param Loader $loader
     * @param string $relPath
     *
     * @return array
     */
    protected function getIconsByPath(Loader $loader, $relPath)
    {
        $icons = [];
        $folder = ExtensionManagementUtility::extPath($loader->getExtensionKey()) . $relPath;
        $extensionPath = ExtensionManagementUtility::extPath($loader->getExtensionKey());
        $files = GeneralUtility::getAllFilesAndFoldersInPath([], $folder, '', false, 99);
        if (!sizeof($files)) {
            return $icons;
        }

        foreach ($files as $path) {
            $provider = BitmapIconProvider::class;
            if (substr(strtolower($path), -3) === 'svg') {
                $provider = SvgIconProvider::class;
            }
            $relativePath = str_replace($extensionPath, '', $path);
            $iconPath = str_replace($relPath, '', $relativePath);

            $pathElements = PathUtility::pathinfo(strtolower(str_replace(['/', '_'], '-', GeneralUtility::camelCaseToLowerCaseUnderscored($iconPath))));
            $icons[] = [
                'provider' => $provider,
                'path' => 'EXT:' . $loader->getExtensionKey() . '/' . $relativePath,
                'identifier' => str_replace('_', '-', $loader->getExtensionKey()) . '-' . $pathElements['filename'],
            ];
        }
        return $icons;
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
     * @param \HDNET\Autoloader\Loader $loader
     * @param array $loaderInformation
     *
     * @internal param \HDNET\Autoloader\Loader $autoLoader
     * @return NULL
     */
    public function loadExtensionConfiguration(Loader $loader, array $loaderInformation)
    {
        if (empty($loaderInformation)) {
            return;
        }

        /** @var IconRegistry $iconRegistry */
        $iconRegistry = GeneralUtility::makeInstance(IconRegistry::class);

        foreach ($loaderInformation as $config) {
            $iconRegistry->registerIcon($config['identifier'], $config['provider'], ['source' => $config['path']]);
        }
        return null;
    }
}
