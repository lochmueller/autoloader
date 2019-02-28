<?php

/**
 * Map String.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Mapper;

use HDNET\Autoloader\MapperInterface;
use HDNET\Autoloader\Utility\ReflectionUtility;

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
        return \in_array(\mb_strtolower($type), [
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
        if (ReflectionUtility::is9orHigher() && $fieldName === 'slug') {
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
}
