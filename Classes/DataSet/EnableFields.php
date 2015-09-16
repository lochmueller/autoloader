<?php
/**
 * DataSet information for enableFields
 *
 * @author Tim Lochmüller
 */

namespace HDNET\Autoloader\DataSet;

use HDNET\Autoloader\DataSetInterface;

/**
 * DataSet information for enableFields
 */
class EnableFields implements DataSetInterface
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
            'ctrl'    => [
                'enablecolumns' => [
                    'disabled'  => 'hidden',
                    'starttime' => 'starttime',
                    'endtime'   => 'endtime',
                    'fe_group'  => 'fe_group',
                ],
            ],
            'columns' => [
                'fe_group'  => $GLOBALS['TCA']['tt_content']['columns']['fe_group'],
                'editlock'  => [
                    'exclude'   => 1,
                    'l10n_mode' => 'mergeIfNotBlank',
                    'label'     => 'LLL:EXT:lang/locallang_tca.xml:editlock',
                    'config'    => [
                        'type' => 'check'
                    ]
                ],
                'hidden'    => [
                    'exclude' => 1,
                    'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
                    'config'  => [
                        'type' => 'check',
                    ],
                ],
                'starttime' => [
                    'exclude'   => 1,
                    'l10n_mode' => 'mergeIfNotBlank',
                    'label'     => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
                    'config'    => [
                        'type'     => 'input',
                        'size'     => 13,
                        'max'      => 20,
                        'eval'     => 'datetime',
                        'checkbox' => 0,
                        'default'  => 0,
                        'range'    => [
                            'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                        ],
                    ],
                ],
                'endtime'   => [
                    'exclude'   => 1,
                    'l10n_mode' => 'mergeIfNotBlank',
                    'label'     => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
                    'config'    => [
                        'type'     => 'input',
                        'size'     => 13,
                        'max'      => 20,
                        'eval'     => 'datetime',
                        'checkbox' => 0,
                        'default'  => 0,
                        'range'    => [
                            'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                        ],
                    ],
                ],
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
            'hidden tinyint(4) unsigned DEFAULT \'0\' NOT NULL',
            'starttime int(11) unsigned DEFAULT \'0\' NOT NULL',
            'endtime int(11) unsigned DEFAULT \'0\' NOT NULL',
            'fe_group varchar(100) DEFAULT \'0\' NOT NULL',
            'editlock tinyint(4) unsigned DEFAULT \'0\' NOT NULL',
        ];
    }

    /**
     * Get database sql key information
     *
     * @return array
     */
    public function getDatabaseSqlKey()
    {
        return [];
    }
}