<?php
/**
 * BackendLayout
 *
 * @category Extension
 * @package  Autoloader\Loader
 * @author   Tim Lochmüller
 */

namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Hooks\BackendLayoutProvider;
use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\Utility\FileUtility;
use HDNET\Autoloader\Utility\IconUtility;
use HDNET\Autoloader\Utility\TranslateUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

/**
 * BackendLayout loader
 *
 * @author Tim Lochmüller
 */
class BackendLayout implements LoaderInterface {

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
	public function prepareLoader(Loader $loader, $type) {
		$backendLayouts = array();
		$commandPath = ExtensionManagementUtility::extPath($loader->getExtensionKey()) . 'Resources/Private/BackendLayouts/';
		$backendLayoutFiles = FileUtility::getBaseFilesWithExtensionInDir($commandPath, 'ts,txt');

		foreach ($backendLayoutFiles as $file) {
			$pathInfo = PathUtility::pathinfo($file);
			$iconPath = 'EXT:' . $loader->getExtensionKey() . '/Resources/Public/Icons/BackendLayouts/' . $pathInfo['filename'] . '.';
			$extension = IconUtility::getIconFileExtension(GeneralUtility::getFileAbsFileName($iconPath));

			$translationKey = 'backendLayout.' . $pathInfo['basename'];
			if ($type === LoaderInterface::EXT_TABLES) {
				TranslateUtility::assureLabel($translationKey, $loader->getExtensionKey(), $pathInfo['filename']);
			}
			$backendLayouts[] = array(
				'path'      => 'EXT:' . $loader->getExtensionKey() . '/Resources/Private/BackendLayouts/' . $file,
				'filename'  => $pathInfo['filename'],
				'icon'      => $extension ? $iconPath . $extension : FALSE,
				'label'     => TranslateUtility::getLllString($translationKey, $loader->getExtensionKey()),
				'extension' => $loader->getExtensionKey(),
			);

		}

		return $backendLayouts;
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
		foreach ($loaderInformation as $backendLayout) {
			BackendLayoutProvider::addBackendLayoutInformation($backendLayout);
		}
	}
}