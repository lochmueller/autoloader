<?php
/**
 * Loading Slots
 *
 * @author Tim Lochmüller
 */

namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\Utility\ClassNamingUtility;
use HDNET\Autoloader\Utility\FileUtility;
use HDNET\Autoloader\Utility\ReflectionUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Reflection\MethodReflection;

/**
 * Loading Slots
 */
class Slots implements LoaderInterface
{

    /**
     * Get all the complex data for the loader.
     * This return value will be cached and stored in the database
     * There is no file monitoring for this cache
     *
     * @param Loader $autoLoader
     * @param int $type
     *
     * @return array
     */
    public function prepareLoader(Loader $autoLoader, $type)
    {
        $slots = [];
        $slotPath = ExtensionManagementUtility::extPath($autoLoader->getExtensionKey()) . 'Classes/Slots/';
        $slotClasses = FileUtility::getBaseFilesInDir($slotPath, 'php');
        $extKey = GeneralUtility::underscoredToUpperCamelCase($autoLoader->getExtensionKey());

        foreach ($slotClasses as $slot) {
            $slotClass = ClassNamingUtility::getFqnByPath(
                $autoLoader->getVendorName(),
                $autoLoader->getExtensionKey(),
                'Slots/' . $slot
            );

            if (!$autoLoader->isInstantiableClass($slotClass)) {
                continue;
            }

            $methods = ReflectionUtility::getPublicMethods($slotClass);
            foreach ($methods as $methodReflection) {
                /** @var MethodReflection $methodReflection */
                $tagConfiguration = ReflectionUtility::getTagConfiguration(
                    $methodReflection,
                    ['signalClass', 'signalName', 'signalPriority']
                );
                foreach ($tagConfiguration['signalClass'] as $key => $signalClass) {
                    if (!isset($tagConfiguration['signalName'][$key])) {
                        continue;
                    }

                    $priority = isset($tagConfiguration['signalPriority'][$key]) ? $tagConfiguration['signalPriority'][$key] : 0;
                    $priority = MathUtility::forceIntegerInRange($priority, 0, 100);

                    $slots[$priority][] = [
                        'signalClassName' => trim($signalClass, '\\'),
                        'signalName' => $tagConfiguration['signalName'][$key],
                        'slotClassNameOrObject' => $slotClass,
                        'slotMethodName' => $methodReflection->getName(),
                    ];
                }
            }
        }

        $slots = $this->flattenSlotsByPriority($slots);

        return $slots;
    }

    /**
     * @param array $slots
     * @return array
     */
    public function flattenSlotsByPriority(array $array)
    {
        krsort($array);
        $result = [];
        foreach ($array as $priority => $slots) {
            foreach ($slots as $slot) {
                $result[] = $slot;
            }
        }

        return $result;
    }

    /**
     * Run the loading process for the ext_tables.php file
     *
     * @param Loader $autoLoader
     * @param array $loaderInformation
     *
     * @return NULL
     */
    public function loadExtensionTables(Loader $autoLoader, array $loaderInformation)
    {
        return null;
    }

    /**
     * Run the loading process for the ext_localconf.php file
     *
     * @param Loader $autoLoader
     * @param array $loaderInformation
     *
     * @return NULL
     */
    public function loadExtensionConfiguration(Loader $autoLoader, array $loaderInformation)
    {
        if (!empty($loaderInformation)) {
            /** @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher */
            $signalSlotDispatcher = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher');
            foreach ($loaderInformation as $slot) {
                $signalSlotDispatcher->connect(
                    $slot['signalClassName'],
                    $slot['signalName'],
                    $slot['slotClassNameOrObject'],
                    $slot['slotMethodName'],
                    true
                );
            }
        }
    }
}
