<?php
/**
 * ExtensionTypoScriptSetup.
 *
 */
namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\SmartObjectRegister;
use HDNET\Autoloader\Utility\ClassNamingUtility;
use HDNET\Autoloader\Utility\ModelUtility;
use HDNET\Autoloader\Utility\ReflectionUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * ExtensionTypoScriptSetup.
 */
class ExtensionTypoScriptSetup implements LoaderInterface
{
    /**
     * Get all the complex data for the loader.
     * This return value will be cached and stored in the database
     * There is no file monitoring for this cache.
     *
     * @param Loader $loader
     * @param int    $type
     *
     * @return array
     */
    public function prepareLoader(Loader $loader, $type)
    {
        // We don't have to prepare anything if the extension has no smart objects
        if (!$this->extensionHasSmartObjects($loader->getExtensionKey())) {
            return [];
        }

        return $this->generateTypoScriptSetup($loader->getExtensionKey());
    }

    /**
     * Run the loading process for the ext_tables.php file.
     *
     * @param Loader $loader
     * @param array  $loaderInformation
     */
    public function loadExtensionTables(Loader $loader, array $loaderInformation)
    {
        $this->addTypoScript($loaderInformation);
    }

    /**
     * Run the loading process for the ext_localconf.php file.
     *
     * @param Loader $loader
     * @param array  $loaderInformation
     */
    public function loadExtensionConfiguration(Loader $loader, array $loaderInformation)
    {
        $this->addTypoScript($loaderInformation);
    }

    /**
     * Add the given loader information as TypoScript.
     *
     * @param array $loaderInformation
     */
    protected function addTypoScript(array $loaderInformation)
    {
        if (!empty($loaderInformation)) {
            ExtensionManagementUtility::addTypoScriptSetup(LF . implode(LF, $loaderInformation) . LF);
        }
    }

    /**
     * Generate the TypoScript setup for the smart objects defined
     * within the extension.
     *
     * @param string $extensionKey
     *
     * @return array
     */
    private function generateTypoScriptSetup($extensionKey)
    {
        $setup = [];
        foreach ($this->getSmartObjectsForExtensionKey($extensionKey) as $className) {
            $table = ModelUtility::getTableNameByModelReflectionAnnotation($className);
            $recordType = (string) ReflectionUtility::getFirstTagValue($className, 'recordType');
            $parentClass = (string) ReflectionUtility::getFirstTagValue($className, 'parentClass');
            if ($table !== '') {
                $setup[] = 'config.tx_extbase.persistence.classes.' . $className . '.mapping.tableName = ' . $table;
            }
            if ($recordType !== '') {
                $setup[] = 'config.tx_extbase.persistence.classes.' . $className . '.mapping.recordType = ' . $recordType;
            }
            if ($parentClass !== '') {
                $setup[] = 'config.tx_extbase.persistence.classes.' . $parentClass . '.subclasses.' . $className . ' = ' . $className;
            }
        }

        return $setup;
    }

    /**
     * Check if the extension has smart objects.
     *
     * @param string $extensionKey
     *
     * @return bool
     */
    private function extensionHasSmartObjects($extensionKey)
    {
        if ($this->getSmartObjectsForExtensionKey($extensionKey)) {
            return true;
        }

        return false;
    }

    /**
     * Get the smart objects for the given extension.
     *
     * @param $extensionKey
     *
     * @return mixed
     */
    private function getSmartObjectsForExtensionKey($extensionKey)
    {
        $smartObjects = SmartObjectRegister::getRegister();
        $extensionObjects = [];
        foreach ($smartObjects as $className) {
            $objectExtension = ClassNamingUtility::getExtensionKeyByModel($className);
            if ($objectExtension === $extensionKey) {
                $extensionObjects[] = $className;
            }
        }

        return $extensionObjects;
    }
}
