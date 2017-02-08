<?php
/**
 * DataSet information for languages
 *
 * @author Tim Lochmüller
 */

namespace HDNET\Autoloader\DataSet;

use HDNET\Autoloader\DataSetInterface;
use HDNET\Autoloader\Utility\ExtendedUtility;

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
        $tca = [
            'ctrl' => [
                'languageField' => 'sys_language_uid',
                'transOrigPointerField' => 'l10n_parent',
                'transOrigDiffSourceField' => 'l10n_diffsource',
            ],
            'columns' => [
                'sys_language_uid' => [
                    'exclude' => 1,
                    'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
                    'config' => [
                        'type' => 'select',
                        'renderType' => 'selectSingle',
                        'default' => '0',
                        'special' => 'languages',
                        'items' => [
                            [
                                'LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages',
                                -1,
                                'flags-multiple'
                            ]
                        ],
                    ],
                ],
                'l10n_parent' => [
                    'displayCond' => 'FIELD:sys_language_uid:>:0',
                    'exclude' => 1,
                    'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
                    'config' => [
                        'type' => 'select',
                        'renderType' => 'selectSingle',
                        'items' => [
                            [
                                '',
                                0
                            ],
                        ],
                        'foreign_table' => $tableName,
                        'foreign_table_where' => 'AND ' . $tableName . '.pid=###CURRENT_PID### AND ' . $tableName . '.sys_language_uid IN (-1,0)',
                        'showIconTable' => false,
                    ],
                ],
                'l10n_diffsource' => [
                    'config' => [
                        'type' => 'passthrough',
                    ],
                ],
            ],
            'palettes' => [
                'language' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource'],
            ],
        ];

        if (!ExtendedUtility::isBranchActive(7)) {
            $tca['columns']['l10n_parent']['config']['iconsInOptionTags'] = false;
            $tca['columns']['l10n_parent']['config']['foreign_table_loadIcons'] = false;
            $tca['columns']['l10n_parent']['config']['noIconsBelowSelect'] = true;
        }

        return $tca;
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
