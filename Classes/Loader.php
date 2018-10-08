<?php

/**
 * Central Loader object.
 */
declare(strict_types=1);

namespace HDNET\Autoloader;

use HDNET\Autoloader\Utility\ReflectionUtility;
use TYPO3\CMS\Core\Cache\Backend\SimpleFileBackend;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Configuration\ConfigurationManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Central Loader object.
 */
class Loader implements SingletonInterface
{
    /**
     * The different implementations and the order of the execution.
     *
     * @var array
     */
    protected $implementations = [
        // class replacement
        'Xclass',
        'AlternativeImplementations',
        // additional functions
        'Hooks',
        'Slots',
        // smart object management
        'SmartObjects',
        'ContentObjects',
        'TcaFiles',
        'ExtensionTypoScriptSetup',
        'ContextSensitiveHelps',
        // non-critical
        'Plugins',
        'FlexForms',
        'CommandController',
        'StaticTyposcript',
        'ExtensionId',
        'TypeConverter',
        'BackendLayout',
        'SoapServer',
        'JsonServer',
        'LanguageOverride',
        'Icon',
        'Gridelement',
        'FluidNamespace',
    ];

    /**
     * The Extension key.
     *
     * @var string
     */
    protected $extensionKey;

    /**
     * The vendorName.
     *
     * @var string
     */
    protected $vendorName;

    /**
     * Set to tro, if there is no valid autoloader cache.
     *
     * @var bool
     */
    protected $disableFirstCall = false;

    /**
     * Default cache configuration.
     *
     * @var array
     */
    protected $cacheConfiguration = [
        'backend' => SimpleFileBackend::class,
        'frontend' => PhpFrontend::class,
        'groups' => [
            'system',
        ],
        'options' => [
            'defaultLifetime' => 0,
        ],
    ];

    /**
     * Build up the object.
     * If there is no valid cache in the LocalConfiguration add one.
     */
    public function __construct()
    {
        if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['autoloader'])) {
            /** @var \TYPO3\CMS\Core\Configuration\ConfigurationManager $configurationManager */
            $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
            $configurationManager->setLocalConfigurationValueByPath(
                'SYS/caching/cacheConfigurations/autoloader',
                $this->cacheConfiguration
            );
            $this->disableFirstCall = true;
        }
    }

    /**
     * Call this method in the ext_tables.php file.
     *
     * @param string $vendorName
     * @param string $extensionKey
     * @param array  $implementations
     */
    public static function extTables($vendorName, $extensionKey, array $implementations = [])
    {
        /** @var \HDNET\Autoloader\Loader $loader */
        $loader = GeneralUtility::makeInstance(self::class);
        $loader->loadExtTables($vendorName, $extensionKey, $implementations);
    }

    /**
     * Call this method in the ext_localconf.php file.
     *
     * @param string $vendorName
     * @param string $extensionKey
     * @param array  $implementations
     */
    public static function extLocalconf($vendorName, $extensionKey, array $implementations = [])
    {
        /** @var \HDNET\Autoloader\Loader $loader */
        $loader = GeneralUtility::makeInstance(self::class);
        $loader->loadExtLocalconf($vendorName, $extensionKey, $implementations);
    }

    /**
     * Load the ext tables information.
     *
     * @param string $vendorName
     * @param string $extensionKey
     * @param array  $implementations
     */
    public function loadExtTables($vendorName, $extensionKey, array $implementations = [])
    {
        if ($this->disableFirstCall) {
            return;
        }
        $this->extensionKey = $extensionKey;
        $this->vendorName = $vendorName;

        $autoLoaderObjects = $this->buildAutoLoaderObjects($implementations);
        $information = $this->prepareAutoLoaderObjects($autoLoaderObjects, LoaderInterface::EXT_TABLES);
        foreach ($autoLoaderObjects as $object) {
            /** @var LoaderInterface $object */
            $informationArray = $information[\get_class($object)];
            if (\is_array($informationArray)) {
                $object->loadExtensionTables($this, $informationArray);
            }
        }
    }

    /**
     * Load the ext localconf information.
     *
     * @param string $vendorName
     * @param string $extensionKey
     * @param array  $implementations
     */
    public function loadExtLocalconf($vendorName, $extensionKey, array $implementations = [])
    {
        if ($this->disableFirstCall) {
            return;
        }
        $this->extensionKey = $extensionKey;
        $this->vendorName = $vendorName;

        $autoLoaderObjects = $this->buildAutoLoaderObjects($implementations);
        $information = $this->prepareAutoLoaderObjects($autoLoaderObjects, LoaderInterface::EXT_LOCAL_CONFIGURATION);
        foreach ($autoLoaderObjects as $object) {
            /** @var LoaderInterface $object */
            $informationArray = $information[\get_class($object)];
            if (\is_array($informationArray)) {
                $object->loadExtensionConfiguration($this, $informationArray);
            }
        }
    }

    /**
     * Get the extension key.
     *
     * @return string
     */
    public function getExtensionKey()
    {
        return $this->extensionKey;
    }

    /**
     * Get the vendor name.
     *
     * @return string
     */
    public function getVendorName()
    {
        return $this->vendorName;
    }

    /**
     * check if the class is loadable and is instantiable
     * (exists and is no interface or abstraction etc.).
     *
     * @param $class
     *
     * @return bool
     */
    public function isInstantiableClass($class)
    {
        return ReflectionUtility::isInstantiable($class);
    }

    /**
     * Build the Autoloader objects.
     *
     * @param array $objectNames
     *
     * @return array
     */
    protected function buildAutoLoaderObjects(array $objectNames = [])
    {
        static $objectCache = [];
        $objectNames = $this->getAutoLoaderNamesInRightOrder($objectNames);
        $objects = [];
        foreach ($objectNames as $autoLoaderObjectName) {
            if (!isset($objectCache[$autoLoaderObjectName])) {
                if (\class_exists('HDNET\\Autoloader\\Loader\\' . $autoLoaderObjectName)) {
                    $objectCache[$autoLoaderObjectName] = GeneralUtility::makeInstance('HDNET\\Autoloader\\Loader\\' . $autoLoaderObjectName);
                } else {
                    $objectCache[$autoLoaderObjectName] = GeneralUtility::makeInstance($autoLoaderObjectName);
                }
            }
            $objects[] = $objectCache[$autoLoaderObjectName];
        }

        return $objects;
    }

    /**
     * Get the Autoloader Names in the right order.
     *
     * @param array $objectNames
     *
     * @return array
     */
    protected function getAutoLoaderNamesInRightOrder(array $objectNames = [])
    {
        if (empty($objectNames)) {
            return $this->implementations;
        }

        // sort
        $names = [];
        foreach ($this->implementations as $className) {
            if (\in_array($className, $objectNames, true)) {
                $names[] = $className;
                unset($objectNames[\array_search($className, $objectNames, true)]);
            }
        }

        return \array_merge($names, $objectNames);
    }

    /**
     * Prepare the autoLoader information.
     *
     * @param array $objects
     * @param int   $type
     *
     * @return array
     */
    protected function prepareAutoLoaderObjects(array $objects, $type)
    {
        $cacheIdentifier = $this->getVendorName() . '_' . $this->getExtensionKey() . '_' . GeneralUtility::shortMD5(\serialize($objects)) . '_' . $type;

        /** @var $cache \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend */
        $cache = $this->getCacheManager()
            ->getCache('autoloader');
        if ($cache->has($cacheIdentifier)) {
            return $cache->requireOnce($cacheIdentifier);
        }

        $return = $this->buildLoaderInformation($objects, $type);
        $cache->set($cacheIdentifier, 'return ' . \var_export($return, true) . ';');

        return $return;
    }

    /**
     * Build the loader information.
     *
     * @param $objects
     * @param $type
     *
     * @return array
     */
    protected function buildLoaderInformation($objects, $type)
    {
        $return = [];
        foreach ($objects as $object) {
            /* @var LoaderInterface $object */
            $return[\get_class($object)] = $object->prepareLoader($this, $type);
        }

        return $return;
    }

    /**
     * Get the cache manager.
     *
     * @return \TYPO3\CMS\Core\Cache\CacheManager
     */
    protected function getCacheManager()
    {
        return GeneralUtility::makeInstance(CacheManager::class);
    }
}
