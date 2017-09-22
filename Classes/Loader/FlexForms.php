<?php
/**
 * Loading FlexForms.
 */

namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\Utility\FileUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Loading FlexForms.
 */
class FlexForms implements LoaderInterface
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
    public function prepareLoader(Loader $loader, $type)
    {
        $flexForms = [];
        $flexFormPath = ExtensionManagementUtility::extPath($loader->getExtensionKey()) . 'Configuration/FlexForms/';

        // Plugins
        $extensionName = GeneralUtility::underscoredToUpperCamelCase($loader->getExtensionKey());
        $flexFormsFiles = FileUtility::getBaseFilesInDir($flexFormPath, 'xml');
        foreach ($flexFormsFiles as $fileKey) {
            $pluginSignature = strtolower($extensionName . '_' . $fileKey);
            $flexForms[] = [
                'pluginSignature' => $pluginSignature,
                'path' => 'FILE:EXT:' . $loader->getExtensionKey() . '/Configuration/FlexForms/' . $fileKey . '.xml',
            ];
        }

        // Content
        $flexFormsFiles = FileUtility::getBaseFilesInDir($flexFormPath . 'Content/', 'xml');
        foreach ($flexFormsFiles as $fileKey) {
            $contentSignature = strtolower($loader->getExtensionKey() . '_' . GeneralUtility::camelCaseToLowerCaseUnderscored($fileKey));
            $flexForms[] = [
                'contentSignature' => $contentSignature,
                'path' => 'FILE:EXT:' . $loader->getExtensionKey() . '/Configuration/FlexForms/Content/' . $fileKey . '.xml',
            ];
        }

        return $flexForms;
    }

    /**
     * Run the loading process for the ext_tables.php file.
     *
     * @param Loader $loader
     * @param array  $loaderInformation
     */
    public function loadExtensionTables(Loader $loader, array $loaderInformation)
    {
        foreach ($loaderInformation as $info) {
            if (isset($info['pluginSignature'])) {
                $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$info['pluginSignature']] = 'layout,select_key,recursive';
                $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$info['pluginSignature']] = 'pi_flexform';
                ExtensionManagementUtility::addPiFlexFormValue($info['pluginSignature'], $info['path']);
            } elseif (isset($info['contentSignature'])) {
                $fields = GeneralUtility::trimExplode(',', $GLOBALS['TCA']['tt_content']['types'][$info['contentSignature']]['showitem']);
                if (!in_array('pi_flexform', $fields)) {
                    $GLOBALS['TCA']['tt_content']['types'][$info['contentSignature']]['showitem'] .= ',pi_flexform';
                }
                ExtensionManagementUtility::addPiFlexFormValue('*', $info['path'], $info['contentSignature']);
            }
        }

        return null;
    }

    /**
     * Run the loading process for the ext_localconf.php file.
     *
     * @param Loader $loader
     * @param array  $loaderInformation
     */
    public function loadExtensionConfiguration(Loader $loader, array $loaderInformation)
    {
    }
}
