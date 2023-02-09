<?php

/**
 * DataSet information for workspaces.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\DataSet;

use HDNET\Autoloader\DataSetInterface;

/**
 * DataSet information for workspaces.
 */
class Workspaces implements DataSetInterface
{
    /**
     * Get TCA information.
     *
     * @return array<string, mixed[]>
     */
    public function getTca(string $tableName): array
    {
        return [
            'ctrl' => [
                'versioningWS' => true,
                'origUid' => 't3_origuid',
            ],
            'columns' => [
            ],
        ];
    }

    /**
     * Get database sql information.
     *
     * @see http://docs.typo3.org/typo3cms/TCAReference/Reference/Ctrl/Index.html
     *
     * @return mixed[]
     */
    public function getDatabaseSql(string $tableName): array
    {
        return [
            //'t3ver_oid int(11) DEFAULT \'0\' NOT NULL',
            //'t3ver_id int(11) DEFAULT \'0\' NOT NULL',
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
     *
     * @return string[]
     */
    public function getDatabaseSqlKey(): array
    {
        return [
            'KEY t3ver_oid (t3ver_oid,t3ver_wsid)',
        ];
    }
}
