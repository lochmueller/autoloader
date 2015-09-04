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
 */
class XliffWriter extends AbstractLocalizationWriter
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
        return '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
<xliff version="1.0">
	<file source-language="en" datatype="plaintext" original="messages" date="' . date('c') . '" product-name="' . $extensionKey . '">
		<header/>
		<body>
		</body>
	</file>
</xliff>';
    }

    /**
     * @param string $extensionKey
     *
     * @return string
     */
    public function getAbsoluteFilename($extensionKey)
    {
        return ExtensionManagementUtility::extPath($extensionKey, 'Resources/Private/Language/locallang.xlf');
    }

    /**
     * Add the Label to the local lang XLIFF
     *
     * @param string $extensionKey
     * @param string $key
     * @param string $default
     *
     * @return NULL
     */
    public function addLabel($extensionKey, $key, $default)
    {
        // Exclude
        if (!strlen($default)) {
            return;
        }
        if (!strlen($key)) {
            return;
        }
        if (!strlen($extensionKey)) {
            return;
        }

        $absolutePath = $this->getAbsoluteFilename($extensionKey);
        $content = GeneralUtility::getUrl($absolutePath);
        $replace = '<body>' . LF . TAB . TAB . TAB . '<trans-unit id="' . $key . '"><source><![CDATA[' . $default . ']]></source></trans-unit>';
        $content = str_replace('<body>', $replace, $content);
        GeneralUtility::writeFile($absolutePath, $content);
        $this->clearCache();
    }
}