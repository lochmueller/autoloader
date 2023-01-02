<?php

declare(strict_types=1);

namespace HDNET\Autoloader;

/**
 * TCA loading interface
 */
interface TcaLoaderInterface extends LoaderInterface
{

    /**
     * Run the loading process for TCA configuration via ModelUtility::getTcaOverrideInformation.
     */
    public function loadTcaConfiguration(Loader $loader, array $loaderInformation, string $extensionKey, string $tableName);
}
