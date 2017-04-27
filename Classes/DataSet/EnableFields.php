<?php
/**
 * DataSet information for enableFields
 *
 * @author Tim Lochmüller
 */

namespace HDNET\Autoloader\DataSet;

use HDNET\Autoloader\DataSetInterface;
use HDNET\Autoloader\Utility\ExtendedUtility;

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
        $tca = [
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
                    'l10n_mode' => 'mergeIfNotBlank',
                    'label' => 'LLL:EXT:lang/locallang_tca.xlf:editlock',
                    'config' => [
                        'type' => 'check'
                    ]
                ],
                'hidden' => [
                    'exclude' => 1,
                    'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
                    'config' => [
                        'type' => 'check',
                    ],
                ],
                'starttime' => [
                    'exclude' => true,
                    'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
                    'config' => [
                        'type' => 'input',
                        'renderType' => 'inputDateTime',
                        'eval' => 'datetime',
                        'default' => 0
                    ],
                    'l10n_mode' => 'exclude',
                    'l10n_display' => 'defaultAsReadonly'
                ],
                'endtime' => [
                    'exclude' => true,
                    'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
                    'config' => [
                        'type' => 'input',
                        'renderType' => 'inputDateTime',
                        'eval' => 'datetime',
                        'default' => 0,
                        'range' => [
                            'upper' => mktime(0, 0, 0, 1, 1, 2038)
                        ]
                    ],
                    'l10n_mode' => 'exclude',
                    'l10n_display' => 'defaultAsReadonly'
                ],
            ],
        ];

        if (ExtendedUtility::isBranchActive('8.0')) {
            unset($tca['columns']['editlock']['l10n_mode']);
            $tca['columns']['editlock']['config']['behaviour']['allowLanguageSynchronization'] = true;
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
