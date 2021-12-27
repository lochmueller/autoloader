<?php

declare(strict_types=1);

namespace HDNET\Autoloader\Service;

use Doctrine\Common\Annotations\AnnotationReader;
use HDNET\Autoloader\Annotation\DatabaseField;
use HDNET\Autoloader\Annotation\EnableRichText;
use HDNET\Autoloader\Mapper;
use HDNET\Autoloader\Service\NameMapperService;
use HDNET\Autoloader\SingletonInterface;
use HDNET\Autoloader\Utility\ExtendedUtility;
use HDNET\Autoloader\Utility\ModelUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TyposcriptConfigurationService implements SingletonInterface
{
    /**
     * @var array
     */
    protected $alreadySerializedCache = [[]];
    /**
     * @var int
     */
    protected $nLevels = [0];

    /**
     * Get a instance of this object.
     *
     * @return TyposcriptConfigurationService
     */
    public static function getInstance()
    {
        return GeneralUtility::makeInstance(self::class);
    }

    public function resetSerializedCache(): void
    {
        $this->nLevels = [0];
        $this->alreadySerializedCache = [[]];
    }

    public function pushSerialisationCache(): void
    {
        $this->nLevels[] = $this->nLevels[\array_key_last($this->nLevels)];
        $this->alreadySerializedCache[] = $this->alreadySerializedCache[\array_key_last($this->alreadySerializedCache)];
    }

    public function popSerialisationCache(): void
    {
        \array_pop($this->nLevels);
        \array_pop($this->alreadySerializedCache);
    }

    /**
     * @param string $modelClassName
     * @param string $extensionKey
     * @param string $tableName
     *
     * @return array
     */
    public function getTyposcriptConfiguration($modelClassName, $extensionKey, $tableName)
    {
        if (\in_array($modelClassName, $this->alreadySerializedCache[\array_key_last($this->alreadySerializedCache)], true)) {
            return [
                "
                uid = INTEGER
                uid {
                    field = uid
                }
                "
            ];
        }
        ++$this->nLevels[\array_key_last($this->nLevels)];
        if ($this->nLevels[\array_key_last($this->nLevels)] > 10) {
            throw new \RuntimeException('More then 10 levels of recursion. Does this code really work?');
            // return [];
        }
        $this->alreadySerializedCache[\array_key_last($this->alreadySerializedCache)][] = $modelClassName;

        $fieldInformation = $this->getCustomModelFields($modelClassName);
        $fields = [];
        $jsonConfiguration = '';
        foreach ($fieldInformation as $info) {
            if ('' === $info['db']) {
                try {
                    $jsonConfiguration = $this->getJsonMappingByVarType($info['var'], $info['property'], $modelClassName, $extensionKey, $tableName);
                } catch (\Exception $exception) {
                    throw new \Exception('Error for mapping in '.$modelClassName.' in property '.$info['property'].' with problem: '.$exception->getMessage(), 123681);
                }
            } else {
                try {
                    $jsonConfiguration = $this->getJsonMappingByVarType($info['db'], $info['property'], $modelClassName, $extensionKey, $tableName);
                } catch (\Exception $ex) {
                    // Do not handle the getDatabaseMappingByVarType by db, Fallback is the var call
                }
            }
            $fields[] = $jsonConfiguration;
        }

        return $fields;
    }

    /**
     * @param string $class
     * @param string $type
     *
     * @return string|null
     */
    public function getRelationDatabaseFieldNameFor($class, $type)
    {
        $fieldInformation = $this->getCustomModelFields($class);
        foreach ($fieldInformation as $info) {
            if ($info['var'] !== $type) {
                continue;
            }
            return GeneralUtility::camelCaseToLowerCaseUnderscored($info['property']);
        }
        return null;
    }

    /**
     * Get the right mapping.
     *
     * @param string $var
     * @param string $fieldName
     * @param string $className
     * @param string $extensionKey
     * @param string $tableName
     *
     * @throws \HDNET\Autoloader\Exception
     *
     * @return string
     */
    protected function getJsonMappingByVarType($var, $fieldName, $className, $extensionKey, $tableName)
    {
        /** @var Mapper $mapper */
        $mapper = ExtendedUtility::create(Mapper::class);

        return $mapper->getJsonDefinition($var, $fieldName, $className, $extensionKey, $tableName);
    }

    /**
     * Get custom database information for the given model.
     */
    protected function getCustomModelFields(string $modelClassName): array
    {
        /** @var AnnotationReader $annotationReader */
        $annotationReader = GeneralUtility::makeInstance(AnnotationReader::class);

        $reflectionClass = new \ReflectionClass($modelClassName);
        $properties = [];
        foreach ($reflectionClass->getProperties() as $property) {
            $propertiesCheck = $annotationReader->getPropertyAnnotation($property, DatabaseField::class);
            if (null !== $propertiesCheck) {
                $properties[$property->getName()] = $propertiesCheck;
            }
        }

        $tableName = ModelUtility::getTableName($modelClassName);
        $nameMapperService = GeneralUtility::makeInstance(NameMapperService::class);
        $fields = [];

        foreach ($properties as $name => $annotation) {
            $var = (string) $annotation->type;
            $fields[] = [
                'property' => $name,
                'name' => $nameMapperService->getDatabaseFieldName($tableName, $name),
                'db' => trim((string) $annotation->sql),
                'var' => trim((string) $var),
                'rte' => null !== $annotationReader->getPropertyAnnotation($reflectionClass->getProperty($name), EnableRichText::class),
            ];
        }

        return $fields;
    }
}
