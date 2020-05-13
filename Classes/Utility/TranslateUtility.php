<?php

/**
 * TranslateUtility.
 */
declare(strict_types = 1);

namespace HDNET\Autoloader\Utility;

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * TranslateUtility.
 */
class TranslateUtility
{
    /**
     * Assure the translation for the given key.
     * If not exists create the label in the xml/xlf file.
     * Returns the localization.
     *
     * Use the Slot to handle the label
     *
     * @see LocalizationUtility::translate
     *
     * @param string $key           key in the localization file
     * @param string $extensionName
     * @param string $default       default value of the label
     * @param array  $arguments     arguments are being passed over to vsprintf
     * @param string $tableName
     *
     * @return string
     */
    public static function assureLabel($key, $extensionName, $default = null, $arguments = null, $tableName = null)
    {
        if (\is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['autoloader']['assureLabel'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['autoloader']['assureLabel'] as $classConfig) {
                $object = GeneralUtility::makeInstance($classConfig);
                if (\is_object($object) && method_exists($object, 'assureLabel')) {
                    $object->assureLabel($key, $extensionName, $default, $arguments, $tableName);
                }
            }
        }

        return (string)$default;
    }

    /**
     * Get the given LLL String or render a help message for the user.
     *
     * @param string $key
     * @param string $extensionKey
     * @param string $tableName
     *
     * @return string
     */
    public static function getLllOrHelpMessage($key, $extensionKey, $tableName = null)
    {
        $lllString = self::getLllString($key, $extensionKey, null, $tableName);
        if (TYPO3_MODE === 'BE' && !isset($GLOBALS['LANG'])) {
            $GLOBALS['LANG'] = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Localization\LanguageService::class);
            $GLOBALS['LANG']->init($GLOBALS['BE_USER']->uc['lang']);
        }
        if (TYPO3_MODE === 'BE' && null === self::getLll($key, $extensionKey, $tableName)) {
            $lllString = self::getLll('pleaseSet', 'autoloader') . $lllString;
            if (isset($GLOBALS['LANG'])) {
                self::assureLabel($key, $extensionKey, $key, null, $tableName);
            }
        }

        return $lllString;
    }

    /**
     * Get the correct LLL string for the given key and extension.
     *
     * @param string $key
     * @param        $extensionKey
     * @param string $file
     * @param string $tableName
     *
     * @return string
     */
    public static function getLllString($key, $extensionKey, $file = null, $tableName = null)
    {
        if (null === $file) {
            $file = 'locallang.xlf';
        }
        if (self::useTableNameFileBase() && null !== $tableName) {
            $file = $tableName . '.xlf';
        }

        return 'LLL:EXT:' . $extensionKey . '/Resources/Private/Language/' . $file . ':' . $key;
    }

    /**
     * Get the translation for the given key.
     *
     * @param string $key
     * @param string $extensionKey
     * @param string $tableName
     *
     * @return string
     */
    public static function getLll($key, $extensionKey, $tableName = null)
    {
        $file = self::getLllString($key, $extensionKey, null, $tableName);

        if (Environment::isCli()) {
            return $file;
        }

        if (\defined('TYPO3_REQUESTTYPE') && TYPO3_REQUESTTYPE === 16) {
            // Do not call the translation workflow in install tool
            return $file;
        }
        if (GeneralUtility::getApplicationContext()->isTesting()) {
            // Do not call translation workflow in testinng
            return $file;
        }

        return LocalizationUtility::translate($file, $extensionKey);
    }

    /**
     * Check if table name file base is used.
     *
     * @return bool
     */
    protected static function useTableNameFileBase()
    {
        $configuration = unserialize((string)$GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['autoloader']);

        return isset($configuration['enableLanguageFileOnTableBase']) ? (bool)$configuration['enableLanguageFileOnTableBase'] : false;
    }
}
