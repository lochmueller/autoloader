<?php

/**
 * Map String.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Mapper;

use HDNET\Autoloader\MapperInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Map String.
 */
class StringMapper implements MapperInterface
{
    /**
     * Check if the current mapper can handle the given type.
     *
     * @param string $type
     */
    public function canHandleType($type): bool
    {
        return 'string' === mb_strtolower($type);
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
        if ('slug' === $fieldName) {
            return [
                'exclude' => 1,
                'label' => $overWriteLabel ?: $fieldName,
                'config' => [
                    'type' => 'slug',
                    'prependSlash' => true,
                    'generatorOptions' => [
                        'fields' => [], // 'title'
                        'prefixParentPageSlug' => true,
                    ],
                    'fallbackCharacter' => '-',
                    'eval' => 'uniqueInSite',
                ],
            ];
        }

        return [
            'exclude' => 1,
            'label' => $overWriteLabel ?: $fieldName,
            'config' => [
                'type' => 'input',
            ],
        ];
    }

    /**
     * Get the database definition for the current mapper.
     */
    public function getDatabaseDefinition(): string
    {
        return 'text';
    }

    public function getJsonDefinition($type, $fieldName, $className, $extensionKey, $tableName)
    {
        $fieldNameUnderscored = GeneralUtility::camelCaseToLowerCaseUnderscored($fieldName);

        return "
        {$fieldName} = TEXT
        {$fieldName} {
            field = {$fieldNameUnderscored}
        }
        ";

        /*
        @todo(flo): Add link mapper for "link" var type?
        link = TEXT
        link.typolink {
            parameter.field = link
            returnLast = url
            forceAbsoluteUrl = 1
        }
        */
    }
}
