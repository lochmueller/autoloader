<?php
/**
 * Loading LanguageOverride.
 */

namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

/**
 * Loading LanguageOverride.
 */
class LanguageOverride implements LoaderInterface
{
    /**
     * Get all the complex data for the loader.
     * This return value will be cached and stored in the database
     * There is no file monitoring for this cache.
     *
     * @param Loader $autoLoader
     * @param int    $type
     *
     * @return array
     */
    public function prepareLoader(Loader $autoLoader, $type)
    {
        $languageOverride = [];
        if (LoaderInterface::EXT_TABLES === $type) {
            return $languageOverride;
        }

        $languageOverridePath = ExtensionManagementUtility::extPath($autoLoader->getExtensionKey()) . 'Resources/Private/Language/Overrides/';
        if (!is_dir($languageOverridePath)) {
            return $languageOverride;
        }

        $files = GeneralUtility::getAllFilesAndFoldersInPath([], $languageOverridePath, 'xlf,php,xml', false, 99);

        foreach ($files as $file) {
            $file = str_replace($languageOverridePath, '', $file);
            $parts = GeneralUtility::trimExplode('/', $file, true);
            $extension = GeneralUtility::camelCaseToLowerCaseUnderscored($parts[0]);
            unset($parts[0]);
            $parts = array_values($parts);

            // language
            $language = 'default';
            $fileParts = GeneralUtility::trimExplode('.', PathUtility::basename($file), true);
            if (2 === strlen($fileParts[0])) {
                $language = $fileParts[0];
                unset($fileParts[0]);
                $parts[count($parts) - 1] = implode('.', $fileParts);
            }

            $languageOverride[] = [
                'language' => $language,
                'original' => 'EXT:' . $extension . '/' . implode('/', $parts),
                'override' => 'EXT:' . $autoLoader->getExtensionKey() . '/Resources/Private/Language/Overrides/' . $file,
            ];
        }

        return $languageOverride;
    }

    /**
     * Run the loading process for the ext_tables.php file.
     *
     * @param Loader $autoLoader
     * @param array  $loaderInformation
     */
    public function loadExtensionTables(Loader $autoLoader, array $loaderInformation)
    {
    }

    /**
     * Run the loading process for the ext_localconf.php file.
     *
     * @param Loader $autoLoader
     * @param array  $loaderInformation
     */
    public function loadExtensionConfiguration(Loader $autoLoader, array $loaderInformation)
    {
        if (!empty($loaderInformation)) {
            foreach ($loaderInformation as $files) {
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride'][$files['language']][$files['original']][] = $files['override'];
            }
        }
    }
}
