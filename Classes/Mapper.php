<?php

/**
 * Mapper for variables types to TCA and DB information.
 */
declare(strict_types=1);

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
     * @var mixed[]
     */
    protected $customMapper = [];

    /**
     * Internal mapper.
     *
     * @var array<class-string<\DateTime>>|string[]
     */
    protected $internalMapper = [
        'Boolean',
        'Float',
        \DateTime::class,
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
     * @param bool|string $overWriteLabel
     *
     * @return mixed[]
     */
    public function getTcaConfiguration(string $type, string $fieldName, $overWriteLabel = false): array
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
     */
    public function getDatabaseDefinition(string $type): string
    {
        $mapper = $this->getMapperByType($type);

        return $mapper->getDatabaseDefinition();
    }

    /**
     * @throws \Exception
     */
    public function getJsonDefinition(string $type, string $fieldName, string $className, string $extensionKey, string $tableName): string
    {
        try {
            $mapper = $this->getMapperByType($type);
        } catch (\Exception $exception) {
            // always return a valid mapper
            $mapper = $this->getMapperByType('String');
        }

        return $mapper->getJsonDefinition($type, $fieldName, $className, $extensionKey, $tableName);
    }

    /**
     * Add a custom mapper.
     */
    public function addCustomMapper(string $className): void
    {
        $this->customMapper[] = $className;
    }

    /**
     * Get a valid mapper for the given type.
     *
     * @throws \Exception
     */
    protected function getMapperByType(string $type): MapperInterface
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
     * @return mixed[]
     */
    protected function getMappers(): array
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
     * @return array<int|string, string>
     */
    protected function getInternalMapperClasses(): array
    {
        $mapper = [];
        foreach ($this->internalMapper as $key => $value) {
            $mapper[$key] = 'HDNET\\Autoloader\\Mapper\\' . $value . 'Mapper';
        }

        return $mapper;
    }
}
