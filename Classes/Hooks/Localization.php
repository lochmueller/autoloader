<?php
/**
 * handler to create the labels
 *
 * @author Tim Lochmüller
 */

namespace HDNET\Autoloader\Hooks;

use HDNET\Autoloader\Localization\LanguageHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * handler to create the labels
 *
 * @hook TYPO3_CONF_VARS|EXTCONF|autoloader|assureLabel
 */
class Localization
{

    /**
     * Take care that the label exists
     *
     * @param string $key       key in the localization file
     * @param string $extensionName
     * @param string $default   default value of the label
     * @param array  $arguments arguments are being passed over to vsprintf
     */
    public function assureLabel($key, $extensionName, &$default, $arguments)
    {
        /** @var LanguageHandler $languageHandler */
        $languageHandler = GeneralUtility::makeInstance('HDNET\\Autoloader\\Localization\\LanguageHandler');
        $default = $languageHandler->handle($key, $extensionName, $default, $arguments);
    }
}
