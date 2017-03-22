<?php

/**
 * StaticInfoTablesMapper
 */

namespace HDNET\Autoloader\Mapper;

use HDNET\Autoloader\MapperInterface;

/**
 * StaticInfoTablesMapper
 */
class StaticInfoTablesMapper implements MapperInterface
{

    /**
     * Class base
     */
    const CLASS_BASE = 'sjbr\\staticinfotables\\domain\\model\\';

    /**
     * @var string
     */
    protected $lastClass;

    /**
     * Check if the current mapper can handle the given type
     *
     * @param string $type
     *
     * @return bool
     */
    public function canHandleType($type)
    {
        $this->lastClass = strtolower($type);
        return !(strpos($this->lastClass, self::CLASS_BASE) === false);
    }

    /**
     * Get the TCA configuration for the current type
     *
     * @param string $fieldName
     * @param bool $overWriteLabel
     *
     * @return array
     */
    public function getTcaConfiguration($fieldName, $overWriteLabel = false)
    {
        $withoutNameSpace = str_replace(self::CLASS_BASE, '', $this->lastClass);

        switch ($withoutNameSpace) {
            case 'country':
                $table = 'static_countries';
                break;
            case 'countryzone':
                $table = 'static_country_zones';
                break;
            case 'currency':
                $table = 'static_currencies';
                break;
            case 'language':
                $table = 'static_languages';
                break;
            case 'territory':
                $table = 'static_territories';
                break;
            default:
                return [];
        }

        return [
            'exclude' => 1,
            'label' => $overWriteLabel ? $overWriteLabel : $fieldName,
            'config' => [
                'type' => 'select',
                'foreign_table' => $table,
            ],
        ];
    }

    /**
     * Get the database definition for the current mapper
     *
     * @return string
     */
    public function getDatabaseDefinition()
    {
        return 'int(11) DEFAULT \'0\' NOT NULL';
    }
}
