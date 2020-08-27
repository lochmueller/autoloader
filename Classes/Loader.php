<?php

/**
 * Central Loader object.
 */
declare(strict_types = 1);

namespace HDNET\Autoloader;

use HDNET\Autoloader\Cache\AutoloaderFileBackend;
use HDNET\Autoloader\Utility\ReflectionUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Central Loader object.
 */
class Loader implements SingletonInterface
{
    /**
     * The different implementations and the order of the execution.
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
        'LanguageOverride',
        'Icon',
        'Gridelement',
        'FluidNamespace',
    ];

    /**
     * The Extension key.
     */
    protected $extensionKey;

    /**
     * The vendorName.
     */
    protected $vendorName;

    /**
     * Call this method in the ext_tables.php file.
     *
     * @param string $vendorName
     * @param string $extensionKey
     */
    public static function extTables($vendorName, $extensionKey, array $implementations = []): void
    {
        /** @var Loader $loader */
        $loader = GeneralUtility::makeInstance(self::class);
        $loader->loadExtTables($vendorName, $extensionKey, $implementations);
    }

    /**
     * Call this method in the ext_localconf.php file.
     *
     * @param string $vendorName
     * @param string $extensionKey
     */
    public static function extLocalconf($vendorName, $extensionKey, array $implementations = []): void
    {
        /** @var Loader $loader */
        $loader = GeneralUtility::makeInstance(self::class);
        $loader->loadExtLocalconf($vendorName, $extensionKey, $implementations);
    }

    /**
     * Load the ext tables information.
     *
     * @param string $vendorName
     * @param string $extensionKey
     */
    public function loadExtTables($vendorName, $extensionKey, array $implementations = []): void
    {
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
     */
    public function loadExtLocalconf($vendorName, $extensionKey, array $implementations = []): void
    {
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
     * @return array
     */
    protected function buildAutoLoaderObjects(array $objectNames = [])
    {
        static $objectCache = [];
        $objectNames = $this->getAutoLoaderNamesInRightOrder($objectNames);
        $objects = [];
        foreach ($objectNames as $autoLoaderObjectName) {
            if (!isset($objectCache[$autoLoaderObjectName])) {
                if (class_exists('HDNET\\Autoloader\\Loader\\' . $autoLoaderObjectName)) {
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
                unset($objectNames[array_search($className, $objectNames, true)]);
            }
        }

        return array_merge($names, $objectNames);
    }

    /**
     * Prepare the autoLoader information.
     *
     * @param int $type
     *
     * @return array
     */
    protected function prepareAutoLoaderObjects(array $objects, $type)
    {
        $cacheIdentifier = $this->getVendorName() . '_' . $this->getExtensionKey() . '_' . GeneralUtility::shortMD5(serialize($objects)) . '_' . $type;

        // Do not use Caching Framework here
        /** @var AutoloaderFileBackend $cacheBackend */
        $cacheBackend = GeneralUtility::makeInstance(AutoloaderFileBackend::class, null);
        if ($cacheBackend->has($cacheIdentifier)) {
            return $cacheBackend->get($cacheIdentifier);
        }

        $return = $this->buildLoaderInformation($objects, $type);
        $cacheBackend->set($cacheIdentifier, $return);

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
            // LoaderInterface
            $return[\get_class($object)] = $object->prepareLoader($this, $type);
        }

        return $return;
    }
}
