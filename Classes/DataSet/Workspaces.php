<?php
/**
 * DataSet information for workspaces
 *
 * @category Extension
 * @package  Autoloader\DataSet
 * @author   Tim Lochmüller
 */

namespace HDNET\Autoloader\DataSet;

use HDNET\Autoloader\DataSetInterface;

/**
 * DataSet information for workspaces
 *
 * @author Tim Lochmüller
 */
class Workspaces implements DataSetInterface {

	/**
	 * Get TCA information
	 *
	 * @param string $tableName
	 *
	 * @return array
	 */
	public function getTca($tableName) {
		return array(
			'ctrl'    => array(
				'versioningWS'                    => 2,
				'versioning_followPages'          => TRUE,
				'shadowColumnsForNewPlaceholders' => 'sys_language_uid',
				'origUid'                         => 't3_origuid'
			),
			'columns' => array(
				't3ver_label' => array(
					'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.versionLabel',
					'config' => array(
						'type' => 'input',
						'size' => 30,
						'max'  => 255,
					)
				)
			)
		);
	}

	/**
	 * Get database sql information
	 *
	 * @param string $tableName
	 *
	 * @return array
	 * @see http://docs.typo3.org/typo3cms/TCAReference/Reference/Ctrl/Index.html
	 */
	public function getDatabaseSql($tableName) {
		return array(
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
		);
	}

	/**
	 * Get database sql key information
	 *
	 * @return array
	 */
	public function getDatabaseSqlKey() {
		return array(
			'KEY t3ver_oid (t3ver_oid,t3ver_wsid)'
		);
	}
}