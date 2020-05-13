<?php

/**
 * Central DataSet object.
 */
declare(strict_types = 1);

namespace HDNET\Autoloader;

use HDNET\Autoloader\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Central DataSet object.
 */
class DataSet implements SingletonInterface
{
    /**
     * The different implementations and the order of the execution.
     */
    protected $implementations = [
        'EnableFields',
        'Language',
        'Workspaces',
    ];

    /**
     * Get all implementations and exclude the given list.
     *
     * @return array
     */
    public function getAllAndExcludeList(array $list)
    {
        $return = $this->implementations;
        foreach ($return as $key => $value) {
            if (\in_array($value, $list, true)) {
                unset($return[$key]);
            }
        }

        return $return;
    }

    /**
     * Get none implementations and include the given list.
     *
     * @return array
     */
    public function getNoneAndIncludeList(array $list)
    {
        return array_diff($this->implementations, $this->getAllAndExcludeList($list));
    }

    /**
     * Get the TCA information of the given data sets.
     *
     * @param $implementations
     * @param $table
     *
     * @return array
     */
    public function getTcaInformation(array $implementations, $table)
    {
        $dataSetObjects = $this->getDataSetObjects($implementations);
        $return = [];
        foreach ($dataSetObjects as $object) {
            $loadedTca = $object->getTca($table);
            /** @var DataSetInterface $object */
            $return = ArrayUtility::mergeRecursiveDistinct($return, $loadedTca);
        }

        return $return;
    }

    /**
     * Get the SQL information of the given data sets.
     *
     * @param $table
     *
     * @return array
     */
    public function getDatabaseSqlInformation(array $implementations, $table)
    {
        $dataSetObjects = $this->getDataSetObjects($implementations);
        $return = [];
        foreach ($dataSetObjects as $object) {
            /** @var DataSetInterface $object */
            $return = array_merge($return, $object->getDatabaseSql($table));
        }

        return $return;
    }

    /**
     * Get the SQL Key information of the given data sets.
     *
     * @param $implementations
     * @param $table
     *
     * @return array
     */
    public function getDatabaseSqlKeyInformation(array $implementations, $table)
    {
        $dataSetObjects = $this->getDataSetObjects($implementations);
        $return = [];
        foreach ($dataSetObjects as $object) {
            /** @var DataSetInterface $object */
            $return = array_merge($return, $object->getDatabaseSqlKey());
        }

        return $return;
    }

    /**
     * Create the data set objects.
     *
     * @return array
     */
    protected function getDataSetObjects(array $implementations)
    {
        foreach ($implementations as $key => $value) {
            $implementations[$key] = GeneralUtility::makeInstance('HDNET\\Autoloader\\DataSet\\' . $value);
        }

        return $implementations;
    }
}
