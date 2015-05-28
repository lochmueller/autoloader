<?php
/**
 * DataSet information for languages
 *
 * @category Extension
 * @package  Autoloader\DataSet
 * @author   Tim Lochmüller
 */

namespace HDNET\Autoloader\DataSet;

use HDNET\Autoloader\DataSetInterface;

/**
 * DataSet information for languages
 *
 * @author Tim Lochmüller
 */
class Language implements DataSetInterface {

	/**
	 * Get TCA information
	 *
	 * @param string $tableName
	 *
	 * @return array
	 */
	public function getTca($tableName) {
		return array(
			'ctrl'     => array(
				'languageField'            => 'sys_language_uid',
				'transOrigPointerField'    => 'l10n_parent',
				'transOrigDiffSourceField' => 'l10n_diffsource',
			),
			'columns'  => array(
				'sys_language_uid' => array(
					'exclude' => 1,
					'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
					'config'  => array(
						'type'                => 'select',
						'foreign_table'       => 'sys_language',
						'foreign_table_where' => 'ORDER BY sys_language.title',
						'items'               => array(
							array(
								'LLL:EXT:lang/locallang_general.xml:LGL.allLanguages',
								-1
							),
							array(
								'LLL:EXT:lang/locallang_general.xml:LGL.default_value',
								0
							)
						),
					),
				),
				'l10n_parent'      => array(
					'displayCond' => 'FIELD:sys_language_uid:>:0',
					'exclude'     => 1,
					'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
					'config'      => array(
						'type'                => 'select',
						'items'               => array(
							array(
								'',
								0
							),
						),
						'foreign_table'       => $tableName,
						'foreign_table_where' => 'AND ' . $tableName . '.pid=###CURRENT_PID### AND ' . $tableName . '.sys_language_uid IN (-1,0)',
					),
				),
				'l10n_diffsource'  => array(
					'config' => array(
						'type' => 'passthrough',
					),
				),
			),
			'palettes' => array(
				'language' => array('showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource'),
			),
		);
	}

	/**
	 * Get database sql information
	 *
	 * @param string $tableName
	 *
	 * @return array
	 */
	public function getDatabaseSql($tableName) {
		return array(
			'sys_language_uid int(11) DEFAULT \'0\' NOT NULL',
			'l10n_parent int(11) DEFAULT \'0\' NOT NULL',
			'l10n_diffsource mediumblob',
		);
	}

	/**
	 * Get database sql key information
	 *
	 * @return array
	 */
	public function getDatabaseSqlKey() {
		return array(
			'KEY language (l10n_parent,sys_language_uid)'
		);
	}
}