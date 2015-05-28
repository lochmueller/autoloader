<?php
/**
 * Central DataSet object
 *
 * @category Extension
 * @package  Autoloader
 * @author   Tim Lochmüller
 */

namespace HDNET\Autoloader;

use HDNET\Autoloader\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Central DataSet object
 *
 * @author Tim Lochmüller
 */
class DataSet implements SingletonInterface {

	/**
	 * The different implementations and the order of the execution
	 *
	 * @var array
	 */
	protected $implementations = array(
		'EnableFields',
		'Language',
		'Workspaces',
	);

	/**
	 * Get all implementations and exclude the given list
	 *
	 * @param array $list
	 *
	 * @return array
	 */
	public function getAllAndExcludeList(array $list) {
		$return = $this->implementations;
		foreach ($return as $key => $value) {
			if (in_array($value, $list)) {
				unset($return[$key]);
			}
		}
		return $return;
	}

	/**
	 * Get none implementations and include the given list
	 *
	 * @param array $list
	 *
	 * @return array
	 */
	public function getNoneAndIncludeList(array $list) {
		return array_diff($this->implementations, $this->getAllAndExcludeList($list));
	}

	/**
	 * Get the TCA information of the given data sets
	 *
	 * @param $implementations
	 * @param $table
	 *
	 * @return array
	 */
	public function getTcaInformation(array $implementations, $table) {
		$dataSetObjects = $this->getDataSetObjects($implementations);
		$return = array();
		foreach ($dataSetObjects as $object) {
			/** @var $object DataSetInterface */
			$return = ArrayUtility::mergeRecursiveDistinct($return, $object->getTca($table));
		}
		return $return;
	}

	/**
	 * Get the SQL information of the given data sets
	 *
	 * @param array $implementations
	 * @param       $table
	 *
	 * @return array
	 */
	public function getDatabaseSqlInformation(array $implementations, $table) {
		$dataSetObjects = $this->getDataSetObjects($implementations);
		$return = array();
		foreach ($dataSetObjects as $object) {
			/** @var $object DataSetInterface */
			$return = array_merge($return, $object->getDatabaseSql($table));
		}
		return $return;

	}

	/**
	 * Get the SQL Key information of the given data sets
	 *
	 * @param $implementations
	 * @param $table
	 *
	 * @return array
	 */
	public function getDatabaseSqlKeyInformation(array $implementations, $table) {

		$dataSetObjects = $this->getDataSetObjects($implementations);
		$return = array();
		foreach ($dataSetObjects as $object) {
			/** @var $object DataSetInterface */
			$return = array_merge($return, $object->getDatabaseSqlKey());
		}
		return $return;
	}

	/**
	 * Create the data set objects
	 *
	 * @param array $implementations
	 *
	 * @return array
	 */
	protected function getDataSetObjects(array $implementations) {
		foreach ($implementations as $key => $value) {
			$implementations[$key] = GeneralUtility::makeInstance('HDNET\\Autoloader\\DataSet\\' . $value);
		}
		return $implementations;
	}
}
