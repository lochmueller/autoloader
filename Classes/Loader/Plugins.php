<?php
/**
 * Loading Plugins
 *
 * @author Tim Lochmüller
 */

namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\Utility\ClassNamingUtility;
use HDNET\Autoloader\Utility\FileUtility;
use HDNET\Autoloader\Utility\ReflectionUtility;
use HDNET\Autoloader\Utility\TranslateUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

/**
 * Loading Plugins
 */
class Plugins implements LoaderInterface
{

    /**
     * Get all the complex data for the loader.
     * This return value will be cached and stored in the database
     * There is no file monitoring for this cache
     *
     * @param Loader $loader
     * @param int    $type
     *
     * @return array
     */
    public function prepareLoader(Loader $loader, $type)
    {
        $pluginInformation = [];

        $controllerPath = ExtensionManagementUtility::extPath($loader->getExtensionKey()) . 'Classes/Controller/';
        $controllers = FileUtility::getBaseFilesRecursivelyInDir($controllerPath, 'php');

        foreach ($controllers as $controller) {
            $controllerName = ClassNamingUtility::getFqnByPath(
                $loader->getVendorName(),
                $loader->getExtensionKey(),
                'Controller/' . $controller
            );
            if (!$loader->isInstantiableClass($controllerName)) {
                continue;
            }

            $controllerKey = str_replace('/', '\\', $controller);
            $controllerKey = str_replace('Controller', '', $controllerKey);

            $methods = ReflectionUtility::getPublicMethods($controllerName);
            foreach ($methods as $method) {
                /** @var $method \TYPO3\CMS\Extbase\Reflection\MethodReflection */
                if ($method->isTaggedWith('plugin')) {
                    $pluginKeys = GeneralUtility::trimExplode(' ', implode(' ', $method->getTagValues('plugin')), true);
                    $actionName = str_replace('Action', '', $method->getName());

                    foreach ($pluginKeys as $pluginKey) {
                        $pluginInformation = $this->addPluginInformation(
                            $pluginInformation,
                            $pluginKey,
                            $controllerKey,
                            $actionName,
                            $method->isTaggedWith('noCache')
                        );
                    }
                }
            }
        }

        return $pluginInformation;
    }

    /**
     * Add the given plugin information to the plugin information array
     *
     * @param array  $pluginInformation
     * @param string $pluginKey
     * @param string $controllerKey
     * @param string $actionName
     * @param bool   $noCache
     *
     * @return array
     */
    protected function addPluginInformation(array $pluginInformation, $pluginKey, $controllerKey, $actionName, $noCache)
    {
        $first = strpos($pluginKey, '!') !== false;
        $pluginKey = trim($pluginKey, '!');

        if (!isset($pluginInformation[$pluginKey])) {
            $pluginInformation[$pluginKey] = [
                'cache'   => [],
                'noCache' => [],
            ];
        }

        $parts = $noCache ? [
            'cache',
            'noCache'
        ] : ['cache'];

        foreach ($parts as $part) {
            if (!isset($pluginInformation[$pluginKey][$part][$controllerKey])) {
                $pluginInformation[$pluginKey][$part][$controllerKey] = '';
            }
            $actions = GeneralUtility::trimExplode(',', $pluginInformation[$pluginKey][$part][$controllerKey], true);
            if ($first) {
                array_unshift($actions, $actionName);
                $targetController = [
                    $controllerKey => $pluginInformation[$pluginKey][$part][$controllerKey]
                ];
                unset($pluginInformation[$pluginKey][$part][$controllerKey]);
                $pluginInformation[$pluginKey][$part] = array_merge($targetController, $pluginInformation[$pluginKey][$part]);
            } else {
                $actions[] = $actionName;
            }


            $pluginInformation[$pluginKey][$part][$controllerKey] = implode(',', $actions);
        }
        return $pluginInformation;
    }

    /**
     * Run the loading process for the ext_tables.php file
     *
     * @param Loader $loader
     * @param array  $loaderInformation
     *
     * @return void
     */
    public function loadExtensionTables(Loader $loader, array $loaderInformation)
    {
        foreach (array_keys($loaderInformation) as $key) {
            $label = TranslateUtility::getLllOrHelpMessage('plugin.' . $key, $loader->getExtensionKey());
            ExtensionUtility::registerPlugin($loader->getExtensionKey(), $key, $label);
        }
    }

    /**
     * Run the loading process for the ext_localconf.php file
     *
     * @param Loader $loader
     * @param array  $loaderInformation
     *
     * @return void
     */
    public function loadExtensionConfiguration(Loader $loader, array $loaderInformation)
    {
        $prefix = $loader->getVendorName() . '.' . $loader->getExtensionKey();
        foreach ($loaderInformation as $key => $information) {
            ExtensionUtility::configurePlugin($prefix, $key, $information['cache'], $information['noCache']);
        }
    }
}
