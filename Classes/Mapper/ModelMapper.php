<?php
/**
 * Map general Models.
 *
 */

namespace HDNET\Autoloader\Mapper;

use HDNET\Autoloader\MapperInterface;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Map general Models.
 */
class ModelMapper implements MapperInterface
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
        if (!class_exists($type)) {
            return false;
        }
        try {
            $dummy = new $type();

            return $dummy instanceof AbstractEntity;
        } catch (\Exception $exception) {
            return false;
        }
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
            'userFunc' => 'HDNET\\Autoloader\\UserFunctions\\Tca->modelInfoField',
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
        return 'int(11) DEFAULT \'0\' NOT NULL';
    }
}
