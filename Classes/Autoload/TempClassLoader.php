<?php
/**
 * TempClassAutoloader.php
 *
 * @author Carsten Biebricher
 */
namespace HDNET\Autoloader\Autoload;

use HDNET\Autoloader\SingletonInterface;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class TempClassLoader
 * Thx to SJBR
 */
class TempClassLoader implements SingletonInterface
{

    /**
     * Cached class loader class name.
     *
     * @var string
     */
    static protected $className = __CLASS__;

    /**
     * Name space of the Domain Model of StaticInfoTables
     *
     * @var string
     */
    static protected $namespace = 'HDNET\\Autoloader\\Xclass\\';

    /**
     * Is TRUE, if the autoloader is registered
     *
     * @var bool
     */
    static protected $isRegistered = false;

    /**
     * Registers the cached class loader.
     *
     * @return bool TRUE in case of success
     */
    public static function registerAutoloader()
    {
        if (self::$isRegistered) {
            return false;
        }

        self::$isRegistered = true;
        return spl_autoload_register(static::$className . '::autoload', true, true);
    }

    /**
     * Autoload function for cached classes.
     *
     * @param string $className Class name
     *
     * @return void
     */
    public static function autoload($className)
    {
        $className = ltrim($className, '\\');

        if (strpos($className, static::$namespace) !== false) {
            $optimizedClassName = str_replace('\\', '', $className);
            $cacheIdentifier = 'XCLASS_' . $optimizedClassName;

            /** @var $cache \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend */
            $cache = GeneralUtility::makeInstance(CacheManager::class)
                ->getCache('autoloader');
            if ($cache->has($cacheIdentifier)) {
                $cache->requireOnce($cacheIdentifier);
            }
        }
    }
}
