<?php

/**
 * Loading Slots.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\Utility\ClassNamingUtility;
use HDNET\Autoloader\Utility\FileUtility;
use HDNET\Autoloader\Utility\ReflectionUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

/**
 * Loading Slots.
 */
class Slots implements LoaderInterface
{
    /**
     * Get all the complex data for the loader.
     * This return value will be cached and stored in the database
     * There is no file monitoring for this cache.
     */
    public function prepareLoader(Loader $loader, int $type): array
    {
        $slots = [];
        $slotPath = ExtensionManagementUtility::extPath($loader->getExtensionKey()) . 'Classes/Slots/';
        $slotClasses = FileUtility::getBaseFilesInDir($slotPath, 'php');

        foreach ($slotClasses as $slot) {
            $slotClass = ClassNamingUtility::getFqnByPath(
                $loader->getVendorName(),
                $loader->getExtensionKey(),
                'Slots/' . $slot
            );

            if (!$loader->isInstantiableClass($slotClass)) {
                continue;
            }

            $methods = ReflectionUtility::getPublicMethodNames($slotClass);
            foreach ($methods as $methodName) {
                $tagConfiguration = ReflectionUtility::getTagConfigurationForMethod($slotClass,
                    $methodName,
                    ['signalClass', 'signalName', 'signalPriority']
                );
                foreach ($tagConfiguration['signalClass'] as $key => $signalClass) {
                    if (!isset($tagConfiguration['signalName'][$key])) {
                        continue;
                    }

                    $priority = $tagConfiguration['signalPriority'][$key] ?? 0;
                    $priority = MathUtility::forceIntegerInRange($priority, 0, 100);

                    $slots[$priority][] = [
                        'signalClassName' => \trim($signalClass, '\\'),
                        'signalName' => $tagConfiguration['signalName'][$key],
                        'slotClassNameOrObject' => $slotClass,
                        'slotMethodName' => $methodName,
                    ];
                }
            }
        }

        $slots = $this->flattenSlotsByPriority($slots);

        return $slots;
    }

    /**
     * Flatten slots by prio.
     *
     * @return array
     */
    public function flattenSlotsByPriority(array $array)
    {
        \krsort($array);
        $result = [];
        foreach ($array as $slots) {
            foreach ($slots as $slot) {
                $result[] = $slot;
            }
        }

        return $result;
    }

    /**
     * Run the loading process for the ext_tables.php file.
     */
    public function loadExtensionTables(Loader $autoLoader, array $loaderInformation)
    {
    }

    /**
     * Run the loading process for the ext_localconf.php file.
     */
    public function loadExtensionConfiguration(Loader $autoLoader, array $loaderInformation)
    {
        if (!empty($loaderInformation)) {
            /** @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher */
            $signalSlotDispatcher = GeneralUtility::makeInstance(Dispatcher::class);
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
