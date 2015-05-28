<?php
/**
 * data set interface
 *
 * @category Extension
 * @package  Autoloader
 * @author   Tim Lochmüller
 */

namespace HDNET\Autoloader;

use HDNET\Autoloader\Loader;

/**
 * data set interface
 *
 * @author Tim Lochmüller
 */
interface DataSetInterface extends SingletonInterface {

	/**
	 * Get TCA information
	 *
	 * @param string $tableName
	 *
	 * @return array
	 */
	public function getTca($tableName);

	/**
	 * Get database sql information
	 *
	 * @param string $tableName
	 *
	 * @return array
	 */
	public function getDatabaseSql($tableName);

	/**
	 * Get database sql key information
	 *
	 * @return array
	 */
	public function getDatabaseSqlKey();
}