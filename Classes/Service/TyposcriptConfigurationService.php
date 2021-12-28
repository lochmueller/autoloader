<?php

declare(strict_types=1);

namespace HDNET\Autoloader\Service;

use HDNET\Autoloader\Mapper;
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
     */
    public static function getInstance(): self
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
        $this->nLevels[] = $this->nLevels[array_key_last($this->nLevels)];
        $this->alreadySerializedCache[] = $this->alreadySerializedCache[array_key_last($this->alreadySerializedCache)];
    }

    public function popSerialisationCache(): void
    {
        array_pop($this->nLevels);
        array_pop($this->alreadySerializedCache);
    }

    /**
     * @return string[]
     */
    public function getTyposcriptConfiguration(string $modelClassName, string $extensionKey, string $tableName): array
    {
        if (\in_array($modelClassName, $this->alreadySerializedCache[array_key_last($this->alreadySerializedCache)], true)) {
            return [
                '
                uid = INTEGER
                uid {
                    field = uid
                }
                ',
            ];
        }
        ++$this->nLevels[array_key_last($this->nLevels)];
        if ($this->nLevels[array_key_last($this->nLevels)] > 10) {
            throw new \RuntimeException('More then 10 levels of recursion. Does this code really work?');
            // return [];
        }
        $this->alreadySerializedCache[array_key_last($this->alreadySerializedCache)][] = $modelClassName;

        $fieldInformation = ModelUtility::getCustomModelFields($modelClassName);
        $fields = [];
        $jsonConfiguration = '';
        foreach ($fieldInformation as $info) {
            if ('' === $info['db']) {
                try {
                    $jsonConfiguration = $this->getJsonMappingByVarType($info['var'], $info['property'], $modelClassName, $extensionKey, $tableName);
                } catch (\Exception $exception) {
                    throw new \Exception('Error for mapping in ' . $modelClassName . ' in property ' . $info['property'] . ' with problem: ' . $exception->getMessage(), 123681, $exception);
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

    public function getRelationDatabaseFieldNameFor(string $class, string $type): ?string
    {
        $fieldInformation = ModelUtility::getCustomModelFields($class);
        foreach ($fieldInformation as $info) {
            if (false === mb_stristr(trim($type, '\\'), \trim($info['var'], '\\'))) {
                continue;
            }

            return GeneralUtility::camelCaseToLowerCaseUnderscored($info['property']);
        }

        return null;
    }

    /**
     * Get the right mapping.
     *
     * @throws \HDNET\Autoloader\Exception
     */
    protected function getJsonMappingByVarType(string $var, string $fieldName, string $className, string $extensionKey, string $tableName): string
    {
        /** @var Mapper $mapper */
        $mapper = ExtendedUtility::create(Mapper::class);

        return $mapper->getJsonDefinition($var, $fieldName, $className, $extensionKey, $tableName);
    }
}
