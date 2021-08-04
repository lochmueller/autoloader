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
     */
    public function getTca(string $tableName): array;

    /**
     * Get database sql information.
     */
    public function getDatabaseSql(string $tableName): array;

    /**
     * Get database sql key information.
     */
    public function getDatabaseSqlKey(): array;
}
