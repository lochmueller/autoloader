<?php
/**
 * Abstraction of the Writer
 *
 * @author  Tim Lochmüller
 */

namespace HDNET\Autoloader\Localization\Writer;

use HDNET\Autoloader\Utility\FileUtility;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Abstraction of the Writer
 */
abstract class AbstractLocalizationWriter implements LocalizationWriterInterface
{

    /**
     * Language file base name
     *
     * @var string
     */
    protected $languageBaseName = 'locallang';

    /**
     * Create default file
     *
     * @param string $extensionKey
     *
     * @return bool
     */
    public function createFileIfNotExists($extensionKey)
    {
        $fileName = $this->getAbsoluteFilename($extensionKey);
        if (is_file($fileName)) {
            return true;
        }
        return FileUtility::writeFileAndCreateFolder($fileName, $this->getBaseFileContent($extensionKey));
    }

    /**
     * flush the l10n caches
     *
     * @return void
     */
    protected function clearCache()
    {
        $caches = [
            't3lib_l10n',
            'l10n'
        ];
        /** @var CacheManager $cacheManager */
        $cacheManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheManager');
        foreach ($caches as $name) {
            try {
                $cache = $cacheManager->getCache($name);
                if ($cache) {
                    $cache->flush();
                }
            } catch (\Exception $ex) {
            }
        }
    }

    /**
     * Get language base name
     *
     * @return string
     */
    public function getLanguageBaseName()
    {
        return $this->languageBaseName;
    }

    /**
     * Set language base name
     *
     * @param string $languageBaseName
     */
    public function setLanguageBaseName($languageBaseName)
    {
        $this->languageBaseName = $languageBaseName;
    }

    /**
     * Wrap CDATA
     *
     * @param string $content
     *
     * @return string
     */
    protected function wrapCdata($content)
    {
        if (htmlentities($content, ENT_NOQUOTES) !== $content) {
            $content = '<![CDATA[' . $content . ']]>';
        }
        return $content;
    }
}
