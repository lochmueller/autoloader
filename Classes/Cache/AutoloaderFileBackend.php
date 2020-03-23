<?php
/**
 *
 */

declare(strict_types=1);

namespace HDNET\Autoloader\Cache;

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * AutoloaderFileBackend
 *
 * Note: This backend is usable without the caching framework
 */
class AutoloaderFileBackend extends \TYPO3\CMS\Core\Cache\Backend\AbstractBackend
{

    /**
     * @inheritDoc
     */
    public function set($entryIdentifier, $data, array $tags = [], $lifetime = null)
    {
        if (is_array($data)) {
            $cacheFile = $this->getCacheFileName($entryIdentifier);
            GeneralUtility::writeFile($cacheFile, '<?php return ' . \var_export($data, true) . ';');
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function get($entryIdentifier)
    {
        if ($this->has($entryIdentifier)) {
            $content = include $this->getCacheFileName($entryIdentifier);
            return $content;
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function has($entryIdentifier)
    {
        return is_file($this->getCacheFileName($entryIdentifier));
    }

    /**
     * @inheritDoc
     */
    public function remove($entryIdentifier)
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function flush()
    {
        var_dump(Environment::getVarPath());
        die();

    }

    /**
     * @inheritDoc
     */
    public function collectGarbage()
    {

        // Write by Loader::class
        return null;
    }


    protected function getCacheFileName($entryIdentifier): string
    {
        return Environment::getVarPath() . '/autoloader_' . $entryIdentifier . '.php';
    }
}
