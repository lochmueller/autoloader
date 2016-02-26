<?php
/**
 * XML writer
 *
 * @author  Tim Lochmüller
 */

namespace HDNET\Autoloader\Localization\Writer;

use HDNET\Autoloader\Utility\FileUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * XML writer
 */
class XmlWriter extends AbstractLocalizationWriter
{

    /**
     * Get the base file content
     *
     * @param string $extensionKey
     *
     * @return string
     */
    public function getBaseFileContent($extensionKey)
    {
        return '<?xml version="1.0"?>
<T3locallang>
	<meta type="array">
	<type>database</type>
	<description>Language file is created via the autoloader for the ' . $extensionKey . ' extension on ' . date(DATE_COOKIE) . '</description>
	</meta>
	<data type="array">
		<languageKey index="default" type="array">
		</languageKey>
	</data>
</T3locallang>';
    }

    /**
     * Get the absolute file name
     *
     * @param string $extensionKey
     *
     * @return string
     */
    public function getAbsoluteFilename($extensionKey)
    {
        return ExtensionManagementUtility::extPath($extensionKey, 'Resources/Private/Language/' . $this->getLanguageBaseName() . '.xml');
    }

    /**
     * Add the label to a XML file
     *
     * @param string $extensionKey
     * @param string $key
     * @param string $default
     *
     * @return NULL
     */
    public function addLabel($extensionKey, $key, $default)
    {
        // Excelude
        if (!strlen($default)) {
            return;
        }
        if (!strlen($key)) {
            return;
        }
        if (!strlen($extensionKey)) {
            return;
        }
        if (GeneralUtility::isFirstPartOfStr($key, 'LLL:')) {
            return;
        }
        $absolutePath = $this->getAbsoluteFilename($extensionKey);
        $content = GeneralUtility::getUrl($absolutePath);
        $replace = '<languageKey index="default" type="array">' . LF . TAB . TAB . TAB . '<label index="' . $key . '"><![CDATA[' . $default . ']]></label>';
        $content = str_replace('<languageKey index="default" type="array">', $replace, $content);
        FileUtility::writeFileAndCreateFolder($absolutePath, $content);
        $this->clearCache();
    }
}
