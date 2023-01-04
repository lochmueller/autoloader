<?php

/**
 * Central Loader object.
 */
declare(strict_types=1);

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
     *
     * @var string[]
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
        'HeadlessJson',
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
     *
     * @var string|null
     */
    protected $extensionKey;

    /**
     * The vendorName.
     *
     * @var string|null
     */
    protected $vendorName;

    /**
     * Call this method in the ext_tables.php file.
     */
    public static function extTables(string $vendorName, string $extensionKey, array $implementations = []): void
    {
        /** @var Loader $loader */
        $loader = GeneralUtility::makeInstance(self::class);
        $GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['Implementations'][$extensionKey] = $implementations;
        $loader->loadExtTables($vendorName, $extensionKey, $implementations);
    }

    /**
     * Call this method in the ext_localconf.php file.
     */
    public static function extLocalconf(string $vendorName, string $extensionKey, array $implementations = []): void
    {
        /** @var Loader $loader */
        $loader = GeneralUtility::makeInstance(self::class);
        $GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['Implementations'][$extensionKey] = $implementations;
        $loader->loadExtLocalconf($vendorName, $extensionKey, $implementations);
    }

    /**
     * @param string|null $extensionKey
     */
    public function setExtensionKey(?string $extensionKey): void
    {
        $this->extensionKey = $extensionKey;
    }

    /**
     * Load the ext tables information.
     */
    public function loadExtTables(string $vendorName, string $extensionKey, array $implementations = []): void
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
     */
    public function loadExtLocalconf(string $vendorName, string $extensionKey, array $implementations = []): void
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
     */
    public function getExtensionKey(): ?string
    {
        return $this->extensionKey;
    }

    /**
     * Get the vendor name.
     */
    public function getVendorName(): ?string
    {
        return $this->vendorName;
    }

    /**
     * check if the class is loadable and is instantiable
     * (exists and is no interface or abstraction etc.).
     *
     * @param $class
     */
    public function isInstantiableClass(string $class): bool
    {
        return ReflectionUtility::isInstantiable($class);
    }

    /**
     * Build the Autoloader objects.
     *
     * @return mixed[]
     */
    public function buildAutoLoaderObjects(array $objectNames = []): array
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
     * @return mixed[]|string[]
     */
    protected function getAutoLoaderNamesInRightOrder(array $objectNames = []): array
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
     * @return array
     */
    public function prepareAutoLoaderObjects(array $objects, int $type)
    {
        $shortHash = substr(md5(serialize($objects)), 0, 10);
        $cacheIdentifier = $this->getVendorName().'_'.$this->getExtensionKey().'_'.$shortHash.'_'.$type;

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
     * @return array<class-string|int, mixed>
     */
    protected function buildLoaderInformation($objects, $type): array
    {
        $return = [];
        foreach ($objects as $object) {
            // LoaderInterface
            $return[\get_class($object)] = $object->prepareLoader($this, $type);
        }

        return $return;
    }
}
