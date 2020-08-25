<?php

/**
 * DataSet information for workspaces.
 */
declare(strict_types = 1);

namespace HDNET\Autoloader\DataSet;

use HDNET\Autoloader\DataSetInterface;

/**
 * DataSet information for workspaces.
 */
class Workspaces implements DataSetInterface
{
    /**
     * Get TCA information.
     */
    public function getTca(string $tableName): array
    {
        return [
            'ctrl' => [
                'versioningWS' => true,
                'shadowColumnsForNewPlaceholders' => 'sys_language_uid',
                'origUid' => 't3_origuid',
            ],
            'columns' => [
                't3ver_label' => [
                    'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
                    'config' => [
                        'type' => 'input',
                        'size' => 30,
                        'max' => 255,
                    ],
                ],
            ],
        ];
    }

    /**
     * Get database sql information.
     *
     * @see http://docs.typo3.org/typo3cms/TCAReference/Reference/Ctrl/Index.html
     */
    public function getDatabaseSql(string $tableName): array
    {
        return [
            //'t3ver_oid int(11) DEFAULT \'0\' NOT NULL',
            //'t3ver_id int(11) DEFAULT \'0\' NOT NULL',
            //'t3ver_label varchar(255) DEFAULT \'\' NOT NULL',
            //'t3ver_wsid int(11) DEFAULT \'0\' NOT NULL',
            //'t3ver_state tinyint(4) DEFAULT \'0\' NOT NULL',
            //'t3ver_stage int(11) DEFAULT \'0\' NOT NULL',
            //'t3ver_count int(11) DEFAULT \'0\' NOT NULL',
            //'t3ver_tstamp int(11) DEFAULT \'0\' NOT NULL',
            //'t3ver_move_id int(11) DEFAULT \'0\' NOT NULL',
            //'t3_origuid int(11) DEFAULT \'0\' NOT NULL',
        ];
    }

    /**
     * Get database sql key information.
     */
    public function getDatabaseSqlKey(): array
    {
        return [
            'KEY t3ver_oid (t3ver_oid,t3ver_wsid)',
        ];
    }
}
