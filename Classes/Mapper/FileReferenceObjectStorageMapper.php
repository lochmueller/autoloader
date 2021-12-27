<?php

/**
 * Map FileReferenceObjectStorage.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Mapper;

use HDNET\Autoloader\MapperInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Map FileReferenceObjectStorage.
 */
class FileReferenceObjectStorageMapper implements MapperInterface
{
    /**
     * Check if the current mapper can handle the given type.
     *
     * @param string $type
     *
     * @return bool
     */
    public function canHandleType($type)
    {
        return \in_array(mb_strtolower(trim($type, '\\')), [
            'typo3\\cms\\extbase\\persistence\\objectstorage<\\typo3\\cms\\extbase\\domain\\model\\filereference>',
        ], true);
    }

    /**
     * Get the TCA configuration for the current type.
     *
     * @param string $fieldName
     * @param bool   $overWriteLabel
     *
     * @return array
     */
    public function getTcaConfiguration($fieldName, $overWriteLabel = false)
    {
        return [
            'exclude' => 1,
            'label' => $overWriteLabel ? $overWriteLabel : $fieldName,
            'config' => ExtensionManagementUtility::getFileFieldTCAConfig($fieldName),
        ];
    }

    /**
     * Get the database definition for the current mapper.
     *
     * @return string
     */
    public function getDatabaseDefinition()
    {
        return 'int(11) DEFAULT \'0\' NOT NULL';
    }

    public function getJsonDefinition($type, $fieldName, $className, $extensionKey, $tableName)
    {
        $fieldNameUnderscored = GeneralUtility::camelCaseToLowerCaseUnderscored($fieldName);

        return "
        {$fieldName} = TEXT
        {$fieldName}.dataProcessing {
            10 = HDNET\\Autoloader\\DataProcessing\\FileProcessor
            10 {
                references.fieldName = {$fieldNameUnderscored}
                references.table = {$tableName}
                as = {$fieldName}
            }
        }
        ";
    }
}
