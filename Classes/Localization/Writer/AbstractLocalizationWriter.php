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
use TYPO3\CMS\Core\Utility\PathUtility;

/**
 * Abstraction of the Writer
 */
abstract class AbstractLocalizationWriter implements LocalizationWriterInterface
{

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
        $caches = array(
            't3lib_l10n',
            'l10n'
        );
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
}
