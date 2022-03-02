<?php

/**
 * Map float/double.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Mapper;

use HDNET\Autoloader\MapperInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Map float/double.
 */
class FloatMapper implements MapperInterface
{
    /**
     * Check if the current mapper can handle the given type.
     *
     * @param string $type
     */
    public function canHandleType($type): bool
    {
        return \in_array(mb_strtolower($type), [
            'float',
            'double',
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
                'type' => 'input',
                'eval' => 'double2',
                'size' => 10,
            ],
        ];
    }

    /**
     * Get the database definition for the current mapper.
     */
    public function getDatabaseDefinition(): string
    {
        return 'varchar(255) DEFAULT \'\' NOT NULL';
    }

    public function getJsonDefinition($type, $fieldName, $className, $extensionKey, $tableName)
    {
        $fieldNameUnderscored = GeneralUtility::camelCaseToLowerCaseUnderscored($fieldName);

        return "
        {$fieldName} = TEXT
        {$fieldName} {
            field = {$fieldNameUnderscored}
        }
        ";
    }
}
