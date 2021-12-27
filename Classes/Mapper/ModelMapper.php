<?php

/**
 * Map general Models.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Mapper;

use HDNET\Autoloader\Mapper;
use HDNET\Autoloader\MapperInterface;
use HDNET\Autoloader\Service\TyposcriptConfigurationService;
use HDNET\Autoloader\Utility\ModelUtility;
use HDNET\Autoloader\Utility\ReflectionUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
     */
    public function canHandleType($type): bool
    {
        return ReflectionUtility::isClassInOtherClassHierarchy($type, AbstractEntity::class);
    }

    /**
     * Get the TCA configuration for the current type.
     *
     * @param string $fieldName
     * @param bool   $overWriteLabel
     *
     * @return array<string, mixed[]>
     */
    public function getTcaConfiguration($fieldName, $overWriteLabel = false): array
    {
        $baseConfig = [
            'type' => 'user',
            'userFunc' => 'HDNET\\Autoloader\\UserFunctions\\Tca->modelInfoField',
        ];

        return [
            'exclude' => 1,
            'label' => $overWriteLabel ?: $fieldName,
            'config' => $baseConfig,
        ];
    }

    /**
     * Get the database definition for the current mapper.
     */
    public function getDatabaseDefinition(): string
    {
        return 'int(11) DEFAULT \'0\' NOT NULL';
    }

    public function getJsonDefinition($type, $fieldName, $className, $extensionKey, $tableName)
    {
        $fieldNameUnderscored = GeneralUtility::camelCaseToLowerCaseUnderscored($fieldName);
        $typeTableName = ModelUtility::getTableName($type);

        $fields = TyposcriptConfigurationService::getInstance()->getTyposcriptConfiguration($type, $extensionKey, $typeTableName);
        $fieldString = \implode("\n", $fields);

        return "
        {$fieldName} = CONTENT_JSON
        {$fieldName} {
            singleItem = 1
            table = {$typeTableName}
            select {
                pidInList = this
                where = {$typeTableName}.uid={field:{$fieldNameUnderscored}}
                where.insertData = 1
            }

            renderObj = JSON
            renderObj.fields {
                {$fieldString}
            }
        }
        ";
    }
}
