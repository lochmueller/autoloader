<?php

/**
 * Loading Gridelements.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\Utility\FileUtility;
use HDNET\Autoloader\Utility\IconUtility;
use HDNET\Autoloader\Utility\TranslateUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

/**
 * Loading Gridelements.
 */
class Gridelement implements LoaderInterface
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
        $grids = [];
        if (!ExtensionManagementUtility::isLoaded('gridelements')) {
            return $grids;
        }

        $commandPath = ExtensionManagementUtility::extPath($loader->getExtensionKey()) . 'Resources/Private/Grids/';
        $files = FileUtility::getBaseFilesWithExtensionInDir($commandPath, 'ts,txt,typoscript');

        foreach ($files as $file) {
            $pathInfo = PathUtility::pathinfo($file);
            $iconPath = 'EXT:' . $loader->getExtensionKey() . '/Resources/Public/Icons/Grids/' . $pathInfo['filename'] . '.';
            $extension = IconUtility::getIconFileExtension(GeneralUtility::getFileAbsFileName($iconPath));

            $translationKey = 'grid.' . $pathInfo['filename'];
            $translationKeyDescription = $translationKey . '.description';
            if (LoaderInterface::EXT_TABLES === $type) {
                TranslateUtility::assureLabel($translationKey, $loader->getExtensionKey(), $pathInfo['filename']);
                TranslateUtility::assureLabel($translationKeyDescription, $loader->getExtensionKey(), $pathInfo['filename'] . ' description ');
            }

            $path = 'EXT:' . $loader->getExtensionKey() . '/Resources/Private/Grids/' . $file;
            $icon = $extension ? $iconPath . $extension : false;
            $label = TranslateUtility::getLllString($translationKey, $loader->getExtensionKey());
            $description = TranslateUtility::getLllString($translationKeyDescription, $loader->getExtensionKey());
            $content = GeneralUtility::getUrl(GeneralUtility::getFileAbsFileName($path));

            $flexForm = 'EXT:' . $loader->getExtensionKey() . '/Configuration/FlexForms/Grids/' . $pathInfo['filename'] . '.xml';
            $flexFormFile = GeneralUtility::getFileAbsFileName($flexForm);
            $flexFormContent = \is_file($flexFormFile) ? GeneralUtility::getUrl($flexFormFile) : false;

            $grids[] = $this->getPageTsConfig($pathInfo['filename'], $label, $content, $icon, $flexFormContent, $description);
        }

        return $grids;
    }

    /**
     * Run the loading process for the ext_tables.php file.
     *
     * @param Loader $loader
     * @param array  $loaderInformation
     */
    public function loadExtensionTables(Loader $loader, array $loaderInformation)
    {
    }

    /**
     * Run the loading process for the ext_localconf.php file.
     *
     * @param Loader $loader
     * @param array  $loaderInformation
     */
    public function loadExtensionConfiguration(Loader $loader, array $loaderInformation)
    {
        if (empty($loaderInformation)) {
            return;
        }

        ExtensionManagementUtility::addPageTSConfig(\implode("\n", $loaderInformation));
    }

    /**
     * Get the Page TS config for the grid.
     *
     * @param $id
     * @param $label
     * @param $config
     * @param $icon
     * @param $flexForm
     * @param $description
     *
     * @return string
     */
    protected function getPageTsConfig($id, $label, $config, $icon, $flexForm, $description)
    {
        $lines = [];
        $lines[] = 'tx_gridelements.setup {';
        $lines[] = $id . ' {';

        $lines[] = 'title = ' . $label;
        $lines[] = 'description = ' . $description;
        if ($icon) {
            $lines[] = 'icon = ' . $icon;
        }
        $lines[] = $config;
        if ($flexForm) {
            $lines[] = 'flexformDS (';
            $lines[] = $flexForm;
            $lines[] = ')';
        }

        $lines[] = '}';
        $lines[] = '}';

        return \implode("\n", $lines);
    }
}
