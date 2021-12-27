<?php

/**
 * Map general ObjectStorage.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Mapper;

use HDNET\Autoloader\MapperInterface;

/**
 * Map general ObjectStorage.
 */
class ObjectStorageMapper implements MapperInterface
{
    /**
     * Check if the current mapper can handle the given type.
     *
     * @param string $type
     */
    public function canHandleType($type): bool
    {
        return false !== mb_stristr(trim($type, '\\'), 'typo3\\cms\\extbase\\persistence\\objectstorage');
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
        $baseConfig = [
            'type' => 'user',
            'userFunc' => 'HDNET\\Autoloader\\UserFunctions\\Tca->objectStorageInfoField',
        ];

        return [
            'exclude' => 1,
            'label' => $overWriteLabel ?: $fieldName,
            'config' => $baseConfig,
        ];
    }

    /**
     * Get the database definition for the current mapper.
     */
    public function getDatabaseDefinition(): string
    {
        return 'varchar(255) DEFAULT \'\' NOT NULL';
    }
}
