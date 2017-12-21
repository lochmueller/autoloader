<?php
/**
 * handler to create the labels.
 */

namespace HDNET\Autoloader\Hooks;

use HDNET\Autoloader\Localization\LanguageHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * handler to create the labels.
 *
 * @hook TYPO3_CONF_VARS|EXTCONF|autoloader|assureLabel
 */
class Localization
{
    /**
     * Take care that the label exists.
     *
     * @param string $key key in the localization file
     * @param string $extensionName
     * @param string $default default value of the label
     * @param array $arguments arguments are being passed over to vsprintf
     * @param string $tableName The tablename of the given table (null, in non table context)
     */
    public function assureLabel($key, $extensionName, &$default, $arguments, $tableName)
    {
        $overrideBaseName = null;
        if($this->useTableNameFileBase()) {
            $overrideBaseName = $tableName;
        }

        /** @var LanguageHandler $languageHandler */
        $languageHandler = GeneralUtility::makeInstance(LanguageHandler::class);
        $default = $languageHandler->handle($key, $extensionName, $default, $arguments, $overrideBaseName);
    }

    /**
     * Check if table name file base is used
     *
     * @return bool
     */
    protected function useTableNameFileBase()
    {
        $configuration = unserialize((string)$GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['autoloader']);
        return isset($configuration['enableLanguageFileOnTableBase']) ? (bool)$configuration['enableLanguageFileOnTableBase'] : false;
    }
}
