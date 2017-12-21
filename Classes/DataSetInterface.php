<?php

/**
 * data set interface.
 */
declare(strict_types=1);

namespace HDNET\Autoloader;

/**
 * data set interface.
 */
interface DataSetInterface extends SingletonInterface
{
    /**
     * Get TCA information.
     *
     * @param string $tableName
     *
     * @return array
     */
    public function getTca(string $tableName): array;

    /**
     * Get database sql information.
     *
     * @param string $tableName
     *
     * @return array
     */
    public function getDatabaseSql(string $tableName): array;

    /**
     * Get database sql key information.
     *
     * @return array
     */
    public function getDatabaseSqlKey(): array;
}
