<?php
/**
 * Loading Plugins
 *
 * @category Extension
 * @package  Autoloader\Loader
 * @author   Tim Lochmüller
 */

namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\Utility\FileUtility;
use HDNET\Autoloader\Utility\ReflectionUtility;
use HDNET\Autoloader\Utility\TranslateUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

/**
 * Loading Plugins
 *
 * @author Tim Lochmüller
 */
class Plugins implements LoaderInterface {

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
		$pluginInformation = array();

		$controllerPath = ExtensionManagementUtility::extPath($loader->getExtensionKey()) . 'Classes/Controller/';
		$controllers = FileUtility::getBaseFilesRecursivelyInDir($controllerPath, 'php');

		$extKey = GeneralUtility::underscoredToUpperCamelCase($loader->getExtensionKey());
		foreach ($controllers as $controller) {
			$controllerName = $loader->getVendorName() . '\\' . $extKey . '\\Controller\\' . str_replace('/', '\\', $controller);

			if (!$loader->isInstantiableClass($controllerName)) {
				continue;
			}

			$controllerKey = str_replace('/', '\\', $controller);
			$controllerKey = str_replace('Controller', '', $controllerKey);

			$methods = ReflectionUtility::getPublicMethods($controllerName);
			foreach ($methods as $method) {
				/** @var $method \TYPO3\CMS\Extbase\Reflection\MethodReflection */
				if ($method->isTaggedWith('plugin')) {
					$pluginKeys = GeneralUtility::trimExplode(' ', implode(' ', $method->getTagValues('plugin')), TRUE);
					$actionName = str_replace('Action', '', $method->getName());

					foreach ($pluginKeys as $pluginKey) {
						$pluginInformation = $this->addPluginInformation($pluginInformation, $pluginKey, $controllerKey, $actionName, $method->isTaggedWith('noCache'));

					}
				}
			}
		}

		return $pluginInformation;
	}

	/**
	 * Add the given plugin information to the plugin information array
	 *
	 * @param array  $pluginInformation
	 * @param string $pluginKey
	 * @param string $controllerKey
	 * @param string $actionName
	 * @param bool   $noCache
	 *
	 * @return array
	 */
	protected function addPluginInformation(array $pluginInformation, $pluginKey, $controllerKey, $actionName, $noCache) {
		if (!isset($pluginInformation[$pluginKey])) {
			$pluginInformation[$pluginKey] = array(
				'cache'   => array(),
				'noCache' => array(),
			);
		}

		$parts = $noCache ? array(
			'cache',
			'noCache'
		) : array('cache');

		foreach ($parts as $part) {
			if (!isset($pluginInformation[$pluginKey][$part][$controllerKey])) {
				$pluginInformation[$pluginKey][$part][$controllerKey] .= $actionName;
			} else {
				$pluginInformation[$pluginKey][$part][$controllerKey] .= ',' . $actionName;
			}
		}
		return $pluginInformation;
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
		foreach (array_keys($loaderInformation) as $key) {
			$label = TranslateUtility::getLllOrHelpMessage('plugin.' . $key, $loader->getExtensionKey());
			ExtensionUtility::registerPlugin($loader->getExtensionKey(), $key, $label);
		}
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
		$prefix = $loader->getVendorName() . '.' . $loader->getExtensionKey();
		foreach ($loaderInformation as $key => $information) {
			ExtensionUtility::configurePlugin($prefix, $key, $information['cache'], $information['noCache']);
		}
	}
}