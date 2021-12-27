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
     *
     * @return bool
     */
    public function canHandleType($type)
    {
        return \in_array(mb_strtolower($type), [
            'string',
        ], true);
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
        if ('slug' === $fieldName) {
            return [
                'exclude' => 1,
                'label' => $overWriteLabel ? $overWriteLabel : $fieldName,
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
            'label' => $overWriteLabel ? $overWriteLabel : $fieldName,
            'config' => [
                'type' => 'input',
            ],
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
