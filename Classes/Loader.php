<?php
/**
 * Central Loader object
 *
 * @category Extension
 * @package  Autoloader
 * @author   Tim Lochmüller
 */

namespace HDNET\Autoloader;

use HDNET\Autoloader\Utility\ReflectionUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Central Loader object
 *
 * @author Tim Lochmüller
 */
class Loader implements SingletonInterface {

	/**
	 * The different implementations and the order of the execution
	 *
	 * @var array
	 */
	protected $implementations = array(
		// class replacement
		'Xclass',
		'AlternativeImplementations',
		'Aspect',
		// additional functions
		'Hooks',
		'Slots',
		// smart object management
		'SmartObjects',
		'ContentObjects',
		'TcaFiles',
		'ExtensionTypoScriptSetup',
		'ContextSensitiveHelps',
		// non-critical
		'Plugins',
		'FlexForms',
		'CommandController',
		'StaticTyposcript',
		'ExtensionId',
		'TypeConverter',
		'BackendLayout',
	);

	/**
	 * The Extension key
	 *
	 * @var string
	 */
	protected $extensionKey;

	/**
	 * The vendorName
	 *
	 * @var string
	 */
	protected $vendorName;

	/**
	 * Set to tro, if there is no valid autoloader cache
	 *
	 * @var bool
	 */
	protected $disableFirstCall = FALSE;

	/**
	 * Default cache configuration
	 *
	 * @var array
	 */
	protected $cacheConfiguration = array(
		'backend'  => 'TYPO3\\CMS\\Core\\Cache\\Backend\\SimpleFileBackend',
		'frontend' => 'TYPO3\\CMS\\Core\\Cache\\Frontend\\PhpFrontend',
		'groups'   => array(
			'system',
		),
		'options'  => array(
			'defaultLifetime' => 0,
		),
	);

	/**
	 * Build up the object.
	 * If there is no valid cache in the LocalConfiguration add one
	 */
	public function __construct() {
		if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['autoloader'])) {
			/** @var \TYPO3\CMS\Core\Configuration\ConfigurationManager $configurationManager */
			$configurationManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Configuration\\ConfigurationManager');
			$configurationManager->setLocalConfigurationValueByPath('SYS/caching/cacheConfigurations/autoloader', $this->cacheConfiguration);
			$this->disableFirstCall = TRUE;
		}
	}

	/**
	 * Call this method in the ext_tables.php file
	 *
	 * @param string $vendorName
	 * @param string $extensionKey
	 * @param array  $implementations
	 *
	 * @return void
	 */
	static public function extTables($vendorName, $extensionKey, array $implementations = array()) {
		/** @var \HDNET\Autoloader\Loader $loader */
		$loader = GeneralUtility::makeInstance('HDNET\\Autoloader\\Loader');
		$loader->loadExtTables($vendorName, $extensionKey, $implementations);
	}

	/**
	 * Call this method in the ext_localconf.php file
	 *
	 * @param string $vendorName
	 * @param string $extensionKey
	 * @param array  $implementations
	 *
	 * @return void
	 */
	static public function extLocalconf($vendorName, $extensionKey, array $implementations = array()) {
		/** @var \HDNET\Autoloader\Loader $loader */
		$loader = GeneralUtility::makeInstance('HDNET\\Autoloader\\Loader');
		$loader->loadExtLocalconf($vendorName, $extensionKey, $implementations);
	}

	/**
	 * Load the ext tables information
	 *
	 * @param string $vendorName
	 * @param string $extensionKey
	 * @param array  $implementations
	 *
	 * @return void
	 */
	public function loadExtTables($vendorName, $extensionKey, array $implementations = array()) {
		if ($this->disableFirstCall) {
			return;
		}
		$this->extensionKey = $extensionKey;
		$this->vendorName = $vendorName;

		$autoLoaderObjects = $this->buildAutoLoaderObjects($implementations);
		$information = $this->prepareAutoLoaderObjects($autoLoaderObjects, LoaderInterface::EXT_TABLES);
		foreach ($autoLoaderObjects as $object) {
			/** @var LoaderInterface $object */
			$informationArray = $information[get_class($object)];
			if (is_array($informationArray)) {
				$object->loadExtensionTables($this, $informationArray);
			}
		}
	}

	/**
	 * Load the ext localconf information
	 *
	 * @param string $vendorName
	 * @param string $extensionKey
	 * @param array  $implementations
	 *
	 * @return void
	 */
	public function loadExtLocalconf($vendorName, $extensionKey, array $implementations = array()) {
		if ($this->disableFirstCall) {
			return;
		}
		$this->extensionKey = $extensionKey;
		$this->vendorName = $vendorName;

		$autoLoaderObjects = $this->buildAutoLoaderObjects($implementations);
		$information = $this->prepareAutoLoaderObjects($autoLoaderObjects, LoaderInterface::EXT_LOCAL_CONFIGURATION);
		foreach ($autoLoaderObjects as $object) {
			/** @var LoaderInterface $object */
			$informationArray = $information[get_class($object)];
			if (is_array($informationArray)) {
				$object->loadExtensionConfiguration($this, $informationArray);
			}
		}
	}

	/**
	 * Build the Autoloader objects
	 *
	 * @param array $objectNames
	 *
	 * @return array
	 */
	protected function buildAutoLoaderObjects(array $objectNames = array()) {
		$objectNames = $this->getAutoLoaderNamesInRightOrder($objectNames);
		$objects = array();
		foreach ($objectNames as $autoLoaderObjectName) {
			$objects[] = GeneralUtility::makeInstance('HDNET\\Autoloader\\Loader\\' . $autoLoaderObjectName);
		}
		return $objects;
	}

	/**
	 * Get the Autoloader Names in the right order
	 *
	 * @param array $objectNames
	 *
	 * @return array
	 */
	protected function getAutoLoaderNamesInRightOrder(array $objectNames = array()) {
		if (!$objectNames) {
			return $this->implementations;
		}

		// sort
		$names = array();
		foreach ($this->implementations as $className) {
			if (in_array($className, $objectNames)) {
				$names[] = $className;
			}
		}

		return $names;
	}

	/**
	 * Prepare the autoLoader information
	 *
	 * @param array $objects
	 * @param int   $type
	 *
	 * @return array
	 */
	protected function prepareAutoLoaderObjects(array $objects, $type) {
		$cacheIdentifier = $this->getVendorName() . '_' . $this->getExtensionKey() . '_' . GeneralUtility::shortMD5(serialize($objects)) . '_' . $type;

		/** @var $cache \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend */
		$cache = $this->getCacheManager()
			->getCache('autoloader');
		if ($cache->has($cacheIdentifier)) {
			return $cache->requireOnce($cacheIdentifier);
		}

		$return = $this->buildLoaderInformation($objects, $type);
		$cache->set($cacheIdentifier, 'return ' . var_export($return, TRUE) . ';');

		return $return;
	}

	/**
	 * Build the loader information
	 *
	 * @param $objects
	 * @param $type
	 *
	 * @return array
	 */
	protected function buildLoaderInformation($objects, $type) {
		$return = array();
		foreach ($objects as $object) {
			/** @var LoaderInterface $object */
			$return[get_class($object)] = $object->prepareLoader($this, $type);
		}
		return $return;
	}

	/**
	 * Get the cache manager
	 *
	 * @return \TYPO3\CMS\Core\Cache\CacheManager
	 */
	protected function getCacheManager() {
		return GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheManager');
	}

	/**
	 * Get the extension key
	 *
	 * @return string
	 */
	public function getExtensionKey() {
		return $this->extensionKey;
	}

	/**
	 * Get the vendor name
	 *
	 * @return string
	 */
	public function getVendorName() {
		return $this->vendorName;
	}

	/**
	 * check if the class is loadable and is instantiable
	 * (exists and is no interface or abstraction etc.)
	 *
	 * @param $class
	 *
	 * @return bool
	 */
	public function isInstantiableClass($class) {
		return ReflectionUtility::isInstantiable($class);
	}
}
