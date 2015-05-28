<?php
/**
 * Custom Backend Preview for Elements like Content Objects.
 *
 * @category Extension
 * @package  Autoloader
 * @author   Carsten Biebricher
 */
namespace HDNET\Autoloader\Hooks;

use HDNET\Autoloader\Utility\ExtendedUtility;
use HDNET\Autoloader\Utility\ModelUtility;
use TYPO3\CMS\Backend\View\PageLayoutView;
use TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ElementBackendPreview
 *
 * @author Carsten Biebricher
 * @see    \TYPO3\CMS\Backend\View\PageLayoutView::tt_content_drawItem
 * @hook   TYPO3_CONF_VARS|SC_OPTIONS|cms/layout/class.tx_cms_layout.php|tt_content_drawItem
 */
class ElementBackendPreview implements PageLayoutViewDrawItemHookInterface {

	/**
	 * Preprocesses the preview rendering of a content element.
	 *
	 * @param PageLayoutView $parentObject  Calling parent object
	 * @param bool           $drawItem      Whether to draw the item using the default functionalities
	 * @param string         $headerContent Header content
	 * @param string         $itemContent   Item content
	 * @param array          $row           Record row of tt_content
	 *
	 * @return void
	 */
	public function preProcess(PageLayoutView &$parentObject, &$drawItem, &$headerContent, &$itemContent, array &$row) {
		if (!$this->isAutoloaderContenobject($row)) {
			return;
		}

		if (!$this->hasBackendPreview($row)) {
			return;
		}

		if (!ExtensionManagementUtility::isLoaded('css_styled_content')) {
			// @todo avoid exception in the backend. Check why the backend is broken
			return;
		}

		$itemContent = $this->getBackendPreview($row);
		$drawItem = FALSE;
	}

	/**
	 * Render the Backend Preview Template and return the HTML.
	 *
	 * @param array $row
	 *
	 * @return string
	 */
	public function getBackendPreview($row) {
		if (!$this->hasBackendPreview($row)) {
			return '';
		}
		$ctype = $row['CType'];
		/** @var array $config */
		$config = $GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['ContentObject'][$ctype];

		$model = ModelUtility::getModel($config['modelClass'], $row, TRUE);

		$view = ExtendedUtility::createExtensionStandaloneView($config['extensionKey'], $config['backendTemplatePath']);
		$view->assignMultiple(array(
			'data'   => $row,
			'object' => $model
		));
		return $view->render();
	}

	/**
	 * Check if the ContentObject has a Backend Preview Template.
	 *
	 * @param array $row
	 *
	 * @return bool
	 */
	public function hasBackendPreview($row) {
		if (!$this->isAutoloaderContenobject($row)) {
			return FALSE;
		}
		$ctype = $row['CType'];
		/** @var array $config */
		$config = $GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['ContentObject'][$ctype];

		$beTemplatePath = GeneralUtility::getFileAbsFileName($config['backendTemplatePath']);
		return is_file($beTemplatePath);
	}

	/**
	 * Check if the the Element is registered by the ContenObject-Autoloader.
	 *
	 * @param array $row
	 *
	 * @return bool
	 */
	public function isAutoloaderContenobject(array $row) {
		$ctype = $row['CType'];
		return (bool)$GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['ContentObject'][$ctype];
	}

}