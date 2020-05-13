<?php

/**
 * StaticInfoTablesMapper.
 */
declare(strict_types = 1);

namespace HDNET\Autoloader\Mapper;

use HDNET\Autoloader\MapperInterface;
use SJBR\StaticInfoTables\Hook\Backend\Form\FormDataProvider\TcaSelectItemsProcessor;
use SJBR\StaticInfoTables\Hook\Backend\Form\Wizard\SuggestReceiver;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * StaticInfoTablesMapper.
 */
class StaticInfoTablesMapper implements MapperInterface
{
    /**
     * Class base.
     */
    const CLASS_BASE = 'sjbr\\staticinfotables\\domain\\model\\';

    /**
     * Last class name.
     */
    protected $lastClass;

    /**
     * Check if the current mapper can handle the given type.
     *
     * @param string $type
     *
     * @return bool
     */
    public function canHandleType($type)
    {
        if (!ExtensionManagementUtility::isLoaded('static_info_tables')) {
            return false;
        }

        $this->lastClass = mb_strtolower($type);

        return !(false === mb_strpos($this->lastClass, self::CLASS_BASE));
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
        $withoutNameSpace = str_replace(self::CLASS_BASE, '', $this->lastClass);

        switch ($withoutNameSpace) {
            case 'country':
                $table = 'static_countries';
                $itemsProcFunc = 'translateCountriesSelector';

                break;
            case 'countryzone':
                $table = 'static_country_zones';
                $itemsProcFunc = 'translateCountryZonesSelector';

                break;
            case 'currency':
                $table = 'static_currencies';
                $itemsProcFunc = 'translateCurrenciesSelector';

                break;
            case 'language':
                $table = 'static_languages';
                $itemsProcFunc = 'translateLanguagesSelector';

                break;
            case 'territory':
                $table = 'static_territories';
                $itemsProcFunc = 'translateTerritoriesSelector';

                break;
            default:
                return [];
        }

        return [
            'exclude' => 1,
            'label' => $overWriteLabel ? $overWriteLabel : $fieldName,
            'config' => [
                'type' => 'select',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
                'items' => [
                    ['', 0],
                ],
                'itemsProcFunc' => TcaSelectItemsProcessor::class . '->' . $itemsProcFunc,
                'foreign_table' => $table,
                'wizards' => [
                    'suggest' => ['type' => 'suggest',
                        'default' => [
                            'receiverClass' => SuggestReceiver::class,
                        ],
                    ],
                ],
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
        return 'int(11) DEFAULT \'0\' NOT NULL';
    }
}
