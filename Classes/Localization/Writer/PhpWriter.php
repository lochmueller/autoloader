<?php
/**
 * PHP Writer
 *
 * @author  Tim Lochmüller
 */

namespace HDNET\Autoloader\Localization\Writer;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * PHP Writer
 */
class PhpWriter extends AbstractLocalizationWriter
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
        $labels = array(
            'default' => array(),
        );
        return $this->getPhpContentByLabels($labels);
    }

    /**
     * Get the absolute path
     *
     * @param string $extensionKey
     *
     * @return string
     */
    public function getAbsoluteFilename($extensionKey)
    {
        return ExtensionManagementUtility::extPath($extensionKey, 'Resources/Private/Language/locallang.php');
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
        include $absolutePath;
        $LOCAL_LANG['default'][$key] = $default;
        GeneralUtility::writeFile($absolutePath, $this->getPhpContentByLabels($LOCAL_LANG));
        $this->clearCache();
    }

    /**
     * Get the right file content
     *
     * @param array $labels
     *
     * @return string
     */
    protected function getPhpContentByLabels(array $labels)
    {
        return '<?php
$LOCAL_LANG = ' . var_export($labels, true) . ';
		?>';
    }
}