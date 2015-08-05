<?php
/**
 * Xliff writer
 *
 * @author  Tim Lochmüller
 */

namespace HDNET\Autoloader\Localization\Writer;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Xliff writer
 *
 * @author Tim Lochmüller
 */
class XliffWriter extends AbstractLocalizationWriter {

	/**
	 * @return string
	 */
	public function getBaseFileContent() {
		// TODO: Implement getBaseFileContent() method.
	}

	/**
	 * @param string $extensionKey
	 *
	 * @return string
	 */
	public function getAbsoluteFilename($extensionKey) {
		return ExtensionManagementUtility::extPath($extensionKey, 'Resources/Private/Language/locallang.xlf');
	}

	/**
	 * Add the Label to the local lang XLIFF
	 *
	 * @param string  $extensionName
	 * @param string  $key
	 * @param string  $value
	 *
	 * @param boolean $createFile
	 *
	 * @return NULL
	 */
	protected function addLabel2Xlf($extensionName, $key, $value, $createFile = FALSE) {
		// Exclude
		if (!strlen($value)) {
			return;
		}
		if (!strlen($key)) {
			return;
		}
		if (!strlen($extensionName)) {
			return;
		}

		$absolutePath = $this->getAbsoluteFilename($extensionName);

		$content = GeneralUtility::getUrl($absolutePath);
		if (strstr($content, '<xliff version="1.0">') === FALSE) {
			return;
		}

		$replace = '<body>' . LF . TAB . TAB . TAB . '<trans-unit id="' . $key . '"><source><![CDATA[' . $value . ']]></source></trans-unit>';
		$content = str_replace('<body>', $replace, $content);
		GeneralUtility::writeFile($absolutePath, $content);
		$this->clearCache();
	}

	/**
	 * Add the label
	 *
	 * @param string $extensionKey
	 * @param string $key
	 * @param string $default
	 *
	 * @return bool
	 */
	public function addLabel($extensionKey, $key, $default) {
		// TODO: Implement addLabel() method.
	}
}
