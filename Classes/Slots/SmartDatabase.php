<?php

declare(strict_types=1);
/**
 * Add the smart object SQL string.
 */

namespace HDNET\Autoloader\Slots;

use HDNET\Autoloader\SmartObjectManager;

/**
 * Add the smart object SQL string.
 */
class SmartDatabase
{
    /**
     * Add the smart object SQL string the the signal below.
     *
     * @signalClass \TYPO3\CMS\Install\Service\SqlExpectedSchemaService
     * @signalName tablesDefinitionIsBeingBuilt
     *
     * @param array $sqlString
     *
     * @return array
     */
    public function loadSmartObjectTables(array $sqlString)
    {
        $sqlString[] = SmartObjectManager::getSmartObjectRegisterSql();

        return ['sqlString' => $sqlString];
    }

    /**
     * Add the smart object SQL string the the signal below.
     *
     * @signalClass \TYPO3\CMS\Extensionmanager\Utility\InstallUtility
     * @signalName tablesDefinitionIsBeingBuilt
     *
     * @param array  $sqlString
     * @param string $extensionKey
     *
     * @return array
     */
    public function updateSmartObjectTables(array $sqlString, $extensionKey)
    {
        $sqlString[] = SmartObjectManager::getSmartObjectRegisterSql();

        return [
            'sqlString' => $sqlString,
            'extensionKey' => $extensionKey,
        ];
    }
}
