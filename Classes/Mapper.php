<?php

/**
 * Mapper for variables types to TCA and DB information.
 */
declare(strict_types = 1);

namespace HDNET\Autoloader;

use HDNET\Autoloader\Utility\ExtendedUtility;

/**
 * Mapper for variables types to TCA and DB information.
 */
class Mapper implements SingletonInterface
{
    /**
     * Custom mapper.
     *
     * @var array
     */
    protected $customMapper = [];

    /**
     * Internal mapper.
     *
     * @var array
     */
    protected $internalMapper = [
        'Boolean',
        'Float',
        'DateTime',
        'FileReference',
        'FileReferenceObjectStorage',
        'ObjectStorage',
        'Int',
        'String',
        'Model',
        'StaticInfoTables',
        'Array',
    ];

    /**
     * Get the TCA configuration for the current type.
     *
     * @param string $type
     * @param string $fieldName
     * @param bool   $overWriteLabel
     *
     * @return array
     */
    public function getTcaConfiguration($type, $fieldName, $overWriteLabel = false)
    {
        try {
            $mapper = $this->getMapperByType($type);
        } catch (\Exception $exception) {
            // always return a valid mapper
            $mapper = $this->getMapperByType('String');
        }

        return $mapper->getTcaConfiguration($fieldName, $overWriteLabel);
    }

    /**
     * Get the database definition for the current mapper.
     *
     * @param string $type
     *
     * @return string
     */
    public function getDatabaseDefinition($type)
    {
        $mapper = $this->getMapperByType($type);

        return $mapper->getDatabaseDefinition();
    }

    /**
     * Add a custom mapper.
     *
     * @param string $className
     */
    public function addCustomMapper($className): void
    {
        $this->customMapper[] = $className;
    }

    /**
     * Get a valid mapper for the given type.
     *
     * @param string $type
     *
     * @throws \Exception
     *
     * @return MapperInterface
     */
    protected function getMapperByType($type)
    {
        $mappers = $this->getMappers();
        foreach ($mappers as $mapper) {
            /** @var MapperInterface $mapper */
            if ($mapper->canHandleType($type)) {
                return $mapper;
            }
        }

        throw new Exception('No valid mapper for the given type found: ' . $type, 123712631);
    }

    /**
     * Get all mappers.
     *
     * @return array
     */
    protected function getMappers()
    {
        $mapper = array_merge($this->customMapper, $this->getInternalMapperClasses());
        foreach ($mapper as $key => $className) {
            $mapper[$key] = ExtendedUtility::create($className);
        }

        return $mapper;
    }

    /**
     * Get internal mapper class names.
     *
     * @return array
     */
    protected function getInternalMapperClasses()
    {
        $mapper = [];
        foreach ($this->internalMapper as $key => $value) {
            $mapper[$key] = 'HDNET\\Autoloader\\Mapper\\' . $value . 'Mapper';
        }

        return $mapper;
    }
}
