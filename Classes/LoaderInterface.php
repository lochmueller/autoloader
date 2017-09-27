<?php
/**
 * General loading interface.
 */
namespace HDNET\Autoloader;

/**
 * General loading interface
 * All Loader are singletons.
 */
interface LoaderInterface extends SingletonInterface
{
    /**
     * Preparation type for (ext_localconf).
     */
    const EXT_LOCAL_CONFIGURATION = 1;

    /**
     * Preparation type for (ext_tables).
     */
    const EXT_TABLES = 2;

    /**
     * Get all the complex data and information for the loader.
     * This return value will be cached and stored in the core_cache of TYPO3.
     * There is no file monitoring for this cache.
     *
     * @param Loader $loader
     * @param int    $type
     *
     * @return array
     */
    public function prepareLoader(Loader $loader, $type);

    /**
     * Run the loading process for the ext_tables.php file.
     *
     * @param Loader $loader
     * @param array  $loaderInformation
     */
    public function loadExtensionTables(Loader $loader, array $loaderInformation);

    /**
     * Run the loading process for the ext_localconf.php file.
     *
     * @param Loader $loader
     * @param array  $loaderInformation
     */
    public function loadExtensionConfiguration(Loader $loader, array $loaderInformation);
}
