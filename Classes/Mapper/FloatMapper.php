<?php
/**
 * Map float/double
 *
 * @author Tim Lochmüller
 */

namespace HDNET\Autoloader\Mapper;

use HDNET\Autoloader\MapperInterface;

/**
 * Map float/double
 */
class FloatMapper implements MapperInterface
{

    /**
     * Check if the current mapper can handle the given type
     *
     * @param string $type
     *
     * @return bool
     */
    public function canHandleType($type)
    {
        return in_array(strtolower($type), [
            'float',
            'double'
        ]);
    }

    /**
     * Get the TCA configuration for the current type
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
            'label'   => $overWriteLabel ? $overWriteLabel : $fieldName,
            'config'  => [
                'type' => 'input',
                'eval' => 'double2',
                'size' => 8,
            ],
        ];
    }

    /**
     * Get the database definition for the current mapper
     *
     * @return string
     */
    public function getDatabaseDefinition()
    {
        return 'varchar(255) DEFAULT \'\' NOT NULL';
    }
}
