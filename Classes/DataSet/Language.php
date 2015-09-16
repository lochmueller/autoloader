<?php
/**
 * DataSet information for languages
 *
 * @author Tim Lochmüller
 */

namespace HDNET\Autoloader\DataSet;

use HDNET\Autoloader\DataSetInterface;

/**
 * DataSet information for languages
 */
class Language implements DataSetInterface
{

    /**
     * Get TCA information
     *
     * @param string $tableName
     *
     * @return array
     */
    public function getTca($tableName)
    {
        return [
            'ctrl'     => [
                'languageField'            => 'sys_language_uid',
                'transOrigPointerField'    => 'l10n_parent',
                'transOrigDiffSourceField' => 'l10n_diffsource',
            ],
            'columns'  => [
                'sys_language_uid' => [
                    'exclude' => 1,
                    'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
                    'config'  => [
                        'type'                => 'select',
                        'foreign_table'       => 'sys_language',
                        'foreign_table_where' => 'ORDER BY sys_language.title',
                        'items'               => [
                            [
                                'LLL:EXT:lang/locallang_general.xml:LGL.allLanguages',
                                -1
                            ],
                            [
                                'LLL:EXT:lang/locallang_general.xml:LGL.default_value',
                                0
                            ]
                        ],
                    ],
                ],
                'l10n_parent'      => [
                    'displayCond' => 'FIELD:sys_language_uid:>:0',
                    'exclude'     => 1,
                    'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
                    'config'      => [
                        'type'                    => 'select',
                        'items'                   => [
                            [
                                '',
                                0
                            ],
                        ],
                        'foreign_table'           => $tableName,
                        'foreign_table_where'     => 'AND ' . $tableName . '.pid=###CURRENT_PID### AND ' . $tableName . '.sys_language_uid IN (-1,0)',
                        'foreign_table_loadIcons' => false,
                        'iconsInOptionTags'       => false,
                        'noIconsBelowSelect'      => true,
                    ],
                ],
                'l10n_diffsource'  => [
                    'config' => [
                        'type' => 'passthrough',
                    ],
                ],
            ],
            'palettes' => [
                'language' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource'],
            ],
        ];
    }

    /**
     * Get database sql information
     *
     * @param string $tableName
     *
     * @return array
     */
    public function getDatabaseSql($tableName)
    {
        return [
            'sys_language_uid int(11) DEFAULT \'0\' NOT NULL',
            'l10n_parent int(11) DEFAULT \'0\' NOT NULL',
            'l10n_diffsource mediumblob',
        ];
    }

    /**
     * Get database sql key information
     *
     * @return array
     */
    public function getDatabaseSqlKey()
    {
        return [
            'KEY language (l10n_parent,sys_language_uid)'
        ];
    }
}