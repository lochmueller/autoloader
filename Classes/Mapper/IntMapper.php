<?php

/**
 * Map int.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Mapper;

use HDNET\Autoloader\MapperInterface;

/**
 * Map int.
 */
class IntMapper implements MapperInterface
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
        return \in_array(\mb_strtolower($type), [
            'int',
            'integer',
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
            'config' => [
                'type' => 'input',
                'eval' => 'int',
                'size' => 8,
            ],
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
}
