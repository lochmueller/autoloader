<?php
/**
 * Create the TCA files
 *
 * @category Extension
 * @package  Autoloader\Loader
 * @author   Tim Lochmüller
 */


namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\SmartObjectManager;

/**
 * Create the TCA files
 *
 * @author Tim Lochmüller
 */
class TcaFiles implements LoaderInterface {

	/**
	 * Get all the complex data for the loader.
	 * This return value will be cached and stored in the database
	 * There is no file monitoring for this cache
	 *
	 * @param Loader $loader
	 * @param int    $type
	 *
	 * @return array
	 */
	public function prepareLoader(Loader $loader, $type) {
		if ($type !== LoaderInterface::EXT_TABLES) {
			return array();
		}

		SmartObjectManager::checkAndCreateTcaInformation();

		// no preparations, because the smart objects fill the register
		return array();
	}

	/**
	 * Run the loading process for the ext_tables.php file
	 *
	 * @param Loader $loader
	 * @param array  $loaderInformation
	 *
	 * @return NULL
	 */
	public function loadExtensionTables(Loader $loader, array $loaderInformation) {
		return NULL;
	}

	/**
	 * Run the loading process for the ext_localconf.php file
	 *
	 * @param Loader $loader
	 * @param array  $loaderInformation
	 *
	 * @return NULL
	 */
	public function loadExtensionConfiguration(Loader $loader, array $loaderInformation) {
		return NULL;
	}
}