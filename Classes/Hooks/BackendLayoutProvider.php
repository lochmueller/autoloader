<?php
/**
 * Add backend layouts
 *
 * @package Autoloader
 * @author  Tim Lochmüller
 */

namespace HDNET\Autoloader\Hooks;

use TYPO3\CMS\Backend\View\BackendLayout\BackendLayout;
use TYPO3\CMS\Backend\View\BackendLayout\BackendLayoutCollection;
use TYPO3\CMS\Backend\View\BackendLayout\DataProviderContext;
use TYPO3\CMS\Backend\View\BackendLayout\DataProviderInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Add backend layouts
 *
 * @author Tim Lochmüller / Thank you Georg for your Idea!
 * @see    https://github.com/georgringer/TYPO3.base/blob/master/typo3conf/ext/theme/Classes/View/BackendLayout/FileProvider.php
 * @hook   TYPO3_CONF_VARS|SC_OPTIONS|BackendLayoutDataProvider
 */
class BackendLayoutProvider implements DataProviderInterface {

	/**
	 * Layout information
	 *
	 * @var array
	 */
	static protected $backendLayoutInformation = array();

	/**
	 * Add one backend layout information
	 *
	 * @param array $backendLayout
	 */
	static public function addBackendLayoutInformation(array $backendLayout) {
		self::$backendLayoutInformation[] = $backendLayout;
	}

	/**
	 * Adds backend layouts to the given backend layout collection.
	 *
	 * @param DataProviderContext     $dataProviderContext
	 * @param BackendLayoutCollection $backendLayoutCollection
	 *
	 * @return void
	 */
	public function addBackendLayouts(DataProviderContext $dataProviderContext, BackendLayoutCollection $backendLayoutCollection) {
		foreach (self::$backendLayoutInformation as $info) {
			$backendLayoutCollection->add($this->createBackendLayout($info));
		}
	}

	/**
	 * Gets a backend layout by (regular) identifier.
	 *
	 * @param string  $identifier
	 * @param integer $pageId
	 *
	 * @return NULL|BackendLayout
	 */
	public function getBackendLayout($identifier, $pageId) {
		foreach (self::$backendLayoutInformation as $info) {
			if ($this->getIdentifier($info) === $identifier) {
				return $this->createBackendLayout($info);
			}
		}
		return NULL;
	}

	/**
	 * Create a backend layout with the given information
	 *
	 * @param $info
	 *
	 * @return mixed
	 */
	protected function createBackendLayout($info) {
		$fileName = GeneralUtility::getFileAbsFileName($info['path']);
		$backendLayout = BackendLayout::create($this->getIdentifier($info), $info['label'], GeneralUtility::getUrl($fileName));
		if ($info['icon']) {
			$backendLayout->setIconPath(str_replace(PATH_site, '', $info['icon']));
		}
		return $backendLayout;
	}

	/**
	 * Get identifier
	 *
	 * @param $info
	 *
	 * @return string
	 */
	protected function getIdentifier($info) {
		return $info['extension'] . '/' . $info['filename'];

	}
}
