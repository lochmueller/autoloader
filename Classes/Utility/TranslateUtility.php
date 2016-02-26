<?php
/**
 * TranslateUtility
 *
 * @author Carsten Biebricher
 */

namespace HDNET\Autoloader\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * TranslateUtility
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
     * @param string $key       key in the localization file
     * @param string $extensionName
     * @param string $default   default value of the label
     * @param array  $arguments arguments are being passed over to vsprintf
     *
     * @return string
     */
    public static function assureLabel($key, $extensionName, $default = null, $arguments = null)
    {
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['autoloader']['assureLabel'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['autoloader']['assureLabel'] as $classConfig) {
                $className = GeneralUtility::getUserObj($classConfig);
                if (is_object($className) && method_exists($className, 'assureLabel')) {
                    $className->assureLabel($key, $extensionName, $default, $arguments);
                }
            }
        }

        return (string)$default;
    }

    /**
     * Get the given LLL String or render a help message for the user
     *
     * @param string $key
     * @param string $extensionKey
     *
     * @return string
     */
    public static function getLllOrHelpMessage($key, $extensionKey)
    {
        $lllString = self::getLllString($key, $extensionKey);
        if (TYPO3_MODE === 'BE' && !isset($GLOBALS['LANG'])) {
            // init for backend
            $GLOBALS['LANG'] = GeneralUtility::makeInstance('TYPO3\\CMS\\Lang\\LanguageService');
            $GLOBALS['LANG']->init($GLOBALS['BE_USER']->uc['lang']);
        }
        if (TYPO3_MODE === 'BE' && self::getLll($key, $extensionKey) === null) {
            $lllString = self::getLll('pleaseSet', 'autoloader') . $lllString;
            if (isset($GLOBALS['LANG'])) {
                self::assureLabel($key, $extensionKey, $key);
            }
        }
        return $lllString;
    }

    /**
     * Get the correct LLL string for the given key and extension
     *
     * @param        $key
     * @param        $extensionKey
     * @param string $file
     *
     * @return string
     */
    static public function getLllString($key, $extensionKey, $file = 'locallang.xlf')
    {
        return 'LLL:EXT:' . $extensionKey . '/Resources/Private/Language/' . $file . ':' . $key;
    }

    /**
     * Get the translation for the given key
     *
     * @param string $key
     * @param string $extensionKey
     *
     * @return string
     */
    static public function getLll($key, $extensionKey)
    {
        $file = self::getLllString($key, $extensionKey);
        return LocalizationUtility::translate($file, $extensionKey);
    }

}