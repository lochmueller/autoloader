<?php
/**
 * Utility functions for the Autoloader
 *
 * @category   Extension
 * @package    Autoloader\Utility
 * @author     Tim Lochmüller <tim@fruit-lab.de>
 */

namespace HDNET\Autoloader\Utility;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Utility functions for the Autoloader
 *
 * @author     Tim Lochmüller <tim@fruit-lab.de>
 */
class ExtendedUtility {

	/**
	 * Create a object with the given class name
	 *
	 * @param string $className
	 *
	 * @return object
	 */
	public static function create($className) {
		$arguments = func_get_args();
		$objManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		return call_user_func_array(array(
			$objManager,
			'get'
		), $arguments);
	}

	/**
	 * Get the query for the given class name oder object
	 *
	 * @param string|object $objectName
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface
	 */
	public static function getQuery($objectName) {
		$objectName = is_object($objectName) ? get_class($objectName) : $objectName;
		/** @var \TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface $manager */
		static $manager = NULL;
		if ($manager === NULL) {
			$manager = self::create('TYPO3\\CMS\\Extbase\\Persistence\\PersistenceManagerInterface');
		}
		return $manager->createQueryForType($objectName);
	}

	/**
	 * Add a xclass/object replacement
	 *
	 * @param $source
	 * @param $target
	 *
	 * @return bool
	 */
	static public function addXclass($source, $target) {
		if (isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][$source])) {
			$message = 'Double registration of Xclass for ' . $source;
			$message .= ' (' . $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][$source]['className'] . ' and ' . $target . ')';
			self::log($message);
			return FALSE;
		}
		$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][$source] = array(
			'className' => $target,
		);
		return TRUE;
	}

	/**
	 * Log into the TYPO3_CONF_VARS to get more information in the backend
	 *
	 * @param $message
	 */
	static public function log($message) {
		if (!is_array($GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['Log'])) {
			$GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['Log'] = array();
		}
		$GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['Log'][] = $message;
	}

	/**
	 * Add a hooks
	 *
	 * @param array  $locations
	 * @param string $configuration
	 */
	static public function addHooks(array $locations, $configuration) {
		foreach ($locations as $location) {
			self::addHook($location, $configuration);
		}
	}

	/**
	 * Add a hook
	 *
	 * @param string $location The location of the hook separated bei pipes
	 * @param string $configuration
	 */
	static public function addHook($location, $configuration) {
		$location = GeneralUtility::trimExplode('|', $location, TRUE);
		array_push($location, 'via_autoloader_' . GeneralUtility::shortMD5($configuration));
		ArrayUtility::setNodes(array(implode('|', $location) => $configuration), $GLOBALS);
	}

	/**
	 * Create a StandaloneView for a extension context
	 *
	 * @param string $extensionKey
	 * @param string $templatePath
	 *
	 * @return \TYPO3\CMS\Fluid\View\StandaloneView
	 */
	static public function createExtensionStandaloneView($extensionKey, $templatePath) {
		$siteRelPath = ExtensionManagementUtility::siteRelPath($extensionKey);
		$templatePath = GeneralUtility::getFileAbsFileName($templatePath);

		/** @var \TYPO3\CMS\Fluid\View\StandaloneView $view */
		$view = self::create('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
		$view->setTemplatePathAndFilename($templatePath);

		$partialPath = $siteRelPath . 'Resources/Private/Partials';
		$layoutPath = $siteRelPath . 'Resources/Private/Layouts';

		if (GeneralUtility::compat_version('7.0')) {
			$view->setPartialRootPaths(array($partialPath));
			$view->setLayoutRootPaths(array($layoutPath));
		} else {
			$view->setPartialRootPath($partialPath);
			$view->setLayoutRootPath($layoutPath);
		}
		return $view;
	}

}