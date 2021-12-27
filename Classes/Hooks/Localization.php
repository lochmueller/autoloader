<?php

/**
 * handler to create the labels.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Hooks;

use HDNET\Autoloader\Localization\LanguageHandler;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * handler to create the labels.
 *
 * Disabled for TYPO3 10!!!! Check!!!
 * -@-H-o-o-k-("TYPO3_CONF_VARS|EXTCONF|autoloader|assureLabel")
 */
class Localization
{
    /**
     * Take care that the label exists.
     *
     * @param string $key       key in the localization file
     * @param string $default   default value of the label
     * @param array  $arguments arguments are being passed over to vsprintf
     * @param string $tableName The tablename of the given table (null, in non table context)
     */
    public function assureLabel(string $key, string $extensionName, string &$default, array $arguments, string $tableName): void
    {
        $overrideBaseName = null;
        if ($this->useTableNameFileBase()) {
            $overrideBaseName = $tableName;
        }

        /** @var LanguageHandler $languageHandler */
        $languageHandler = GeneralUtility::makeInstance(LanguageHandler::class);
        $default = $languageHandler->handle($key, $extensionName, $default, $arguments, $overrideBaseName);
    }

    /**
     * Check if table name file base is used.
     */
    protected function useTableNameFileBase(): bool
    {
        $configuration = (array)GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('autoloader');

        return isset($configuration['enableLanguageFileOnTableBase']) ? (bool)$configuration['enableLanguageFileOnTableBase'] : false;
    }
}
