<?php

/**
 * Map boolean.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Mapper;

use HDNET\Autoloader\MapperInterface;

/**
 * Map boolean.
 */
class BooleanMapper implements MapperInterface
{
    /**
     * Check if the current mapper can handle the given type.
     *
     * @param string $type
     */
    public function canHandleType($type): bool
    {
        return \in_array(mb_strtolower($type), [
            'bool',
            'boolean',
        ], true);
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
            'config' => [
                'type' => 'check',
            ],
        ];
    }

    /**
     * Get the database definition for the current mapper.
     */
    public function getDatabaseDefinition(): string
    {
        return 'tinyint(4) unsigned DEFAULT \'0\' NOT NULL';
    }
}
