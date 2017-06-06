<?php
/**
 * DataSet information for workspaces
 *
 * @author Tim Lochmüller
 */

namespace HDNET\Autoloader\DataSet;

use HDNET\Autoloader\DataSetInterface;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

/**
 * DataSet information for workspaces
 */
class Workspaces implements DataSetInterface
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
        $isModernTca = VersionNumberUtility::convertVersionNumberToInteger(TYPO3_branch) >= VersionNumberUtility::convertVersionNumberToInteger('8.0');
        $tca = [
            'ctrl'    => [
                'versioningWS'                    => $isModernTca ? true : 2,
                'versioning_followPages'          => true,
                'shadowColumnsForNewPlaceholders' => 'sys_language_uid',
                'origUid'                         => 't3_origuid'
            ],
            'columns' => [
                't3ver_label' => [
                    'label'  => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
                    'config' => [
                        'type' => 'input',
                        'size' => 30,
                        'max'  => 255,
                    ]
                ]
            ]
        ];
        if ($isModernTca) {
            unset($tca['ctrl']['versioning_followPages']);
        }
        return $tca;
    }

    /**
     * Get database sql information
     *
     * @param string $tableName
     *
     * @return array
     * @see http://docs.typo3.org/typo3cms/TCAReference/Reference/Ctrl/Index.html
     */
    public function getDatabaseSql($tableName)
    {
        return [
            't3ver_oid int(11) DEFAULT \'0\' NOT NULL',
            't3ver_id int(11) DEFAULT \'0\' NOT NULL',
            't3ver_label varchar(255) DEFAULT \'\' NOT NULL',
            't3ver_wsid int(11) DEFAULT \'0\' NOT NULL',
            't3ver_state tinyint(4) DEFAULT \'0\' NOT NULL',
            't3ver_stage int(11) DEFAULT \'0\' NOT NULL',
            't3ver_count int(11) DEFAULT \'0\' NOT NULL',
            't3ver_tstamp int(11) DEFAULT \'0\' NOT NULL',
            't3ver_move_id int(11) DEFAULT \'0\' NOT NULL',
            't3_origuid int(11) DEFAULT \'0\' NOT NULL',
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
            'KEY t3ver_oid (t3ver_oid,t3ver_wsid)'
        ];
    }
}
