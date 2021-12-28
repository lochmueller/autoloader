<?php

/**
 * Map FileReference.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Mapper;

use HDNET\Autoloader\MapperInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Map FileReference.
 */
class FileReferenceMapper implements MapperInterface
{
    /**
     * Check if the current mapper can handle the given type.
     *
     * @param string $type
     */
    public function canHandleType($type): bool
    {
        return 'typo3\\cms\\extbase\\domain\\model\\filereference' === mb_strtolower(trim($type, '\\'));
    }

    /**
     * Get the TCA configuration for the current type.
     *
     * @param string $fieldName
     * @param bool   $overWriteLabel
     *
     * @return array<string, mixed[]>
     */
    public function getTcaConfiguration($fieldName, $overWriteLabel = false): array
    {
        return [
            'exclude' => 1,
            'label' => $overWriteLabel ?: $fieldName,
            'config' => ExtensionManagementUtility::getFileFieldTCAConfig($fieldName, ['maxitems' => 1]),
        ];
    }

    /**
     * Get the database definition for the current mapper.
     */
    public function getDatabaseDefinition(): string
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
