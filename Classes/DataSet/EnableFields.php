<?php

/**
 * DataSet information for enableFields.
 */
declare(strict_types = 1);

namespace HDNET\Autoloader\DataSet;

use HDNET\Autoloader\DataSetInterface;

/**
 * DataSet information for enableFields.
 */
class EnableFields implements DataSetInterface
{
    /**
     * Get TCA information.
     */
    public function getTca(string $tableName): array
    {
        return [
            'ctrl' => [
                'enablecolumns' => [
                    'disabled' => 'hidden',
                    'starttime' => 'starttime',
                    'endtime' => 'endtime',
                    'fe_group' => 'fe_group',
                ],
            ],
            'columns' => [
                'fe_group' => $GLOBALS['TCA']['tt_content']['columns']['fe_group'],
                'editlock' => [
                    'exclude' => 1,
                    'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:editlock',
                    'config' => [
                        'type' => 'check',
                        'behaviour' => [
                            'allowLanguageSynchronization' => true,
                        ],
                    ],
                ],
                'hidden' => [
                    'exclude' => 1,
                    'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
                    'config' => [
                        'type' => 'check',
                    ],
                ],
                'starttime' => [
                    'exclude' => true,
                    'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
                    'config' => [
                        'type' => 'input',
                        'renderType' => 'inputDateTime',
                        'eval' => 'datetime',
                        'default' => 0,
                    ],
                    'l10n_mode' => 'exclude',
                    'l10n_display' => 'defaultAsReadonly',
                ],
                'endtime' => [
                    'exclude' => true,
                    'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
                    'config' => [
                        'type' => 'input',
                        'renderType' => 'inputDateTime',
                        'eval' => 'datetime',
                        'default' => 0,
                        'range' => [
                            'upper' => mktime(0, 0, 0, 1, 1, 2038),
                        ],
                    ],
                    'l10n_mode' => 'exclude',
                    'l10n_display' => 'defaultAsReadonly',
                ],
            ],
        ];
    }

    /**
     * Get database sql information.
     */
    public function getDatabaseSql(string $tableName): array
    {
        return [
            'hidden tinyint(4) unsigned DEFAULT \'0\' NOT NULL',
            'starttime int(11) unsigned DEFAULT \'0\' NOT NULL',
            'endtime int(11) unsigned DEFAULT \'0\' NOT NULL',
            'fe_group varchar(100) DEFAULT \'0\' NOT NULL',
            'editlock tinyint(4) unsigned DEFAULT \'0\' NOT NULL',
        ];
    }

    /**
     * Get database sql key information.
     */
    public function getDatabaseSqlKey(): array
    {
        return [];
    }
}
