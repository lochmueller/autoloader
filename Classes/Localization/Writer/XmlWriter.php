<?php
/**
 * XML writer
 *
 * @author  Tim Lochmüller
 */

namespace HDNET\Autoloader\Localization\Writer;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * XML writer
 */
class XmlWriter extends AbstractLocalizationWriter {

	/**
	 * @return string
	 */
	public function getBaseFileContent() {
		return '<?xml version="1.0"?>
<T3locallang>
	<meta type="array">
	<type>database</type>
	<description>Language file is created via the Autoloader extension on ' . date(DATE_COOKIE) . '</description>
	</meta>
	<data type="array">
		<languageKey index="default" type="array">
		</languageKey>
	</data>
</T3locallang>';
	}

	/**
	 * @param string $extensionKey
	 *
	 * @return string
	 */
	public function getAbsoluteFilename($extensionKey) {
		return ExtensionManagementUtility::extPath($extensionKey, 'Resources/Private/Language/locallang.xml');
	}

	/**
	 * Add the label to a XML file
	 *
	 * @param         $extensionName
	 * @param         $key
	 * @param         $value
	 *
	 * @param boolean $createFile
	 *
	 * @return NULL
	 */
	public function addLabel2Xml($extensionName, $key, $value, $createFile = FALSE) {
		// Excelude
		if (!strlen($value)) {
			return;
		}
		if (!strlen($key)) {
			return;
		}
		if (!strlen($extensionName)) {
			return;
		}
		if (GeneralUtility::isFirstPartOfStr($key, 'LLL:')) {
			return;
		}
		$absolutePath = $this->getAbsoluteFilename($extensionName);

		$content = GeneralUtility::getUrl($absolutePath);
		if (strstr($content, '<languageKey index="default" type="array">') === FALSE) {
			return;
		}
		$replace = '<languageKey index="default" type="array">' . LF . TAB . TAB . TAB . '<label index="' . $key . '"><![CDATA[' . $value . ']]></label>';
		$content = str_replace('<languageKey index="default" type="array">', $replace, $content);
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
