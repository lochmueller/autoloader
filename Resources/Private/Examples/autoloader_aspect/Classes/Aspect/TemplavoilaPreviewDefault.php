<?php
/**
 * TemplavoilaPreviewDefault.php
 * This aspect make ContentObject BackendPreview for templavoila possible.
 *
 * @category Extension
 * @package  AutoloaderAspect\Aspect
 * @author   Carsten Biebricher
 */

namespace HDNET\AutoloaderAspect\Aspect;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * TemplavoilaPreviewDefault
 *
 * @see    tx_templavoila_preview_default
 * @author Carsten Biebricher
 */
class TemplavoilaPreviewDefault {

	/**
	 * Enable for the ContentObjects in templavoila a backend preview.
	 * Check if the Element is a AutoloaderContentObject and have a BackendPreview-Template.
	 * On default without ContenObjects it render the normal templavoila default preview.
	 *
	 * @param object $object class of the joinPoint
	 * @param array  $params arguments of the joinPoint
	 *
	 * @aspectClass     tx_templavoila_preview_default
	 * @aspectJoinPoint render_previewContent
	 * @aspectAdvice    after
	 *
	 * @return array
	 */
	public function render_previewContentAfter($object, $params) {
		$row = $params['args'][0];

		/** @var \HDNET\Autoloader\Hooks\ElementBackendPreview $elementBackendPreview */
		$elementBackendPreview = GeneralUtility::makeInstance('HDNET\\Autoloader\\Hooks\\ElementBackendPreview');

		if (!$elementBackendPreview->isAutoloaderContenobject($row)) {
			return $params;
		}

		if (!$elementBackendPreview->hasBackendPreview($row)) {
			return $params;
		}

		$preview = $elementBackendPreview->getBackendPreview($row);

		$params['result'] = $preview;

		return $params;
	}
}