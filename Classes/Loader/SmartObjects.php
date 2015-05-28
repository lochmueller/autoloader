<?php
/**
 * Loading SmartObjects
 *
 * @category Extension
 * @package  Autoloader\Loader
 * @author   Tim Lochmüller
 */


namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\SmartObjectManager;
use HDNET\Autoloader\SmartObjectRegister;
use HDNET\Autoloader\Utility\FileUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Loading SmartObjects
 *
 * @author Tim Lochmüller
 */
class SmartObjects implements LoaderInterface {

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
		$configuration = array();
		$modelPath = ExtensionManagementUtility::extPath($loader->getExtensionKey()) . 'Classes/Domain/Model/';
		if (!is_dir($modelPath)) {
			return $configuration;
		}

		$models = FileUtility::getBaseFilesInDir($modelPath, 'php');
		foreach ($models as $model) {
			$className = $loader->getVendorName() . '\\' . ucfirst(GeneralUtility::underscoredToUpperCamelCase($loader->getExtensionKey())) . '\\Domain\\Model\\' . $model;
			if (SmartObjectManager::isSmartObjectClass($className)) {
				$configuration[] = $className;
			}
		}
		// already add for the following processes
		$this->addClassesToSmartRegister($configuration);

		return $configuration;
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
		$this->addClassesToSmartRegister($loaderInformation);
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
		$this->addClassesToSmartRegister($loaderInformation);
		return NULL;
	}

	/**
	 * Add the given classes to the SmartObject Register
	 *
	 * @param array $loaderInformation
	 */
	protected function addClassesToSmartRegister($loaderInformation) {
		foreach ($loaderInformation as $configuration) {
			SmartObjectRegister::register($configuration);
		}
	}
}