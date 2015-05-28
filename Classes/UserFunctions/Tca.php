<?php
/**
 * Tca UserFunctions
 *
 * @category Extension
 * @package  Autoloader\UserFunctions
 * @author   Carsten Biebricher
 */

namespace HDNET\Autoloader\UserFunctions;

/**
 * Tca UserFunctions
 *
 * @author Carsten Biebricher
 */
class Tca {

	/**
	 * Generate the help message for object storage fields
	 *
	 * @param array                              $configuration
	 * @param \TYPO3\CMS\Backend\Form\FormEngine $formEngine
	 *
	 * @return string
	 */
	public function objectStorageInfoField($configuration, $formEngine) {
		return $this->generateGenericRelationMessage($configuration);
	}

	/**
	 * Generate the help message for model fields
	 *
	 * @param array                              $configuration
	 * @param \TYPO3\CMS\Backend\Form\FormEngine $formEngine
	 *
	 * @return string
	 */
	public function modelInfoField($configuration, $formEngine) {
		return $this->generateGenericRelationMessage($configuration);
	}

	/**
	 * Get a generic text for an info box
	 *
	 * @param array $configuration
	 *
	 * @return string
	 */
	protected function generateGenericRelationMessage($configuration) {
		$infoField = '<strong>Please configure your TCA for this field.</strong><br/>';
		$infoField .= 'You see this message because you have NOT configured the TCA.';
		$infoField .= '<ul><li>table: <em>' . $configuration['table'] . '</em></li>';
		$infoField .= '<li>field: <em>' . $configuration['field'] . '</em></li>';
		$infoField .= '<li>config-file';
		$infoField .= '<ul><li>own table: <em>Configuration/TCA/' . $configuration['table'] . '.php</em></li>';
		$infoField .= '<li>foreign table: <em>Configuration/TCA/Overrides/' . $configuration['table'] . '.php</em></li></ul>';
		$infoField .= '</li></ul>';
		$infoField .= 'Common foreign tables are <em>tt_content</em>, <em>tt_address</em>, &hellip;.<br/><br/>';
		$infoField .= 'Information about proper TCA configuration as ';
		$infoField .= '<a href="http://docs.typo3.org/typo3cms/TCAReference/Reference/Columns/Group/Index.html" target="_blank">group</a>, ';
		$infoField .= '<a href="http://docs.typo3.org/typo3cms/TCAReference/Reference/Columns/Inline/Index.html" target="_blank">inline</a> or ';
		$infoField .= '<a href="http://docs.typo3.org/typo3cms/TCAReference/Reference/Columns/Select/Index.html" target="_blank">select</a>';
		$infoField .= '-type can be found in the TCA-documentation.<br/>';
		return $this->wrapInInfoBox($infoField);
	}

	/**
	 * Wrap the given content in a info box for the backend
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	protected function wrapInInfoBox($content) {
		return '<div style="padding: 5px; border: 2px solid red;">' . $content . '</div>';
	}
}
