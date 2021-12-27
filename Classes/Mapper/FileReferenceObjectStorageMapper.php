<?php

/**
 * Map FileReferenceObjectStorage.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Mapper;

use HDNET\Autoloader\MapperInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Map FileReferenceObjectStorage.
 */
class FileReferenceObjectStorageMapper implements MapperInterface
{
    /**
     * Check if the current mapper can handle the given type.
     *
     * @param string $type
     */
    public function canHandleType($type): bool
    {
        return 'typo3\\cms\\extbase\\persistence\\objectstorage<\\typo3\\cms\\extbase\\domain\\model\\filereference>' === mb_strtolower(trim($type, '\\'));
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
            'config' => ExtensionManagementUtility::getFileFieldTCAConfig($fieldName),
        ];
    }

    /**
     * Get the database definition for the current mapper.
     */
    public function getDatabaseDefinition(): string
    {
        return 'int(11) DEFAULT \'0\' NOT NULL';
    }
}
