<?php

/**
 * Mapper Interface.
 */
declare(strict_types=1);

namespace HDNET\Autoloader;

/**
 * Mapper Interface.
 */
interface MapperInterface extends SingletonInterface
{
    /**
     * Check if the current mapper can handle the given type.
     *
     * @param string $type
     *
     * @return bool
     */
    public function canHandleType($type);

    /**
     * Get the TCA configuration for the current type.
     *
     * @param string $fieldName
     * @param bool   $overWriteLabel
     *
     * @return array
     */
    public function getTcaConfiguration($fieldName, $overWriteLabel = false);

    /**
     * Get the database definition for the current mapper.
     *
     * @return string
     */
    public function getDatabaseDefinition();

    /**
     * Gets the json definition from the headless typoscript for the current mapper.
     *
     * @param string $type
     * @param string $fieldName
     * @param string $className
     * @param string $extensionKey
     * @param string $tableName
     *
     * @return string
     */
    public function getJsonDefinition($type, $fieldName, $className, $extensionKey, $tableName);
}
