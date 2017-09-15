<?php
/**
 * Map Array.
 *
 */

namespace HDNET\Autoloader\Mapper;

use HDNET\Autoloader\MapperInterface;

/**
 * Map Array.
 */
class ArrayMapper implements MapperInterface
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
        return in_array(strtolower($type), [
            'array',
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
        $baseConfig = [
            'type' => 'user',
            'userFunc' => 'HDNET\\Autoloader\\UserFunctions\\Tca->arrayInfoField',
        ];

        return [
            'exclude' => 1,
            'label' => $overWriteLabel ? $overWriteLabel : $fieldName,
            'config' => $baseConfig,
        ];
    }

    /**
     * Get the database definition for the current mapper.
     *
     * @return string
     */
    public function getDatabaseDefinition()
    {
        return 'text';
    }
}
