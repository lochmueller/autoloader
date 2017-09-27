<?php
/**
 * Map DateTime.
 */
namespace HDNET\Autoloader\Mapper;

use HDNET\Autoloader\MapperInterface;

/**
 * Map DateTime.
 */
class DateTimeMapper implements MapperInterface
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
        return in_array(strtolower(trim($type, '\\')), [
            'datetime',
        ]);
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
                'renderType' => 'inputDateTime',
                'eval' => 'datetime',
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
