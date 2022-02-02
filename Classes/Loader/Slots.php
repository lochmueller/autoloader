<?php

/**
 * Loading Slots.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Loader;

use Doctrine\Common\Annotations\AnnotationReader;
use HDNET\Autoloader\Annotation\SignalClass;
use HDNET\Autoloader\Annotation\SignalName;
use HDNET\Autoloader\Annotation\SignalPriority;
use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\Utility\ClassNamingUtility;
use HDNET\Autoloader\Utility\FileUtility;
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
     *
     * @return mixed[]
     */
    public function prepareLoader(Loader $loader, int $type): array
    {
        $slots = [];
        /** @var AnnotationReader $annotationReader */
        $annotationReader = GeneralUtility::makeInstance(AnnotationReader::class);
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
            $reflectionClass = new \ReflectionClass($slotClass);

            foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                $signalClassAnnotation = $annotationReader->getMethodAnnotation($method, SignalClass::class);
                if (null !== $signalClassAnnotation && null != ($priority = $annotationReader->getMethodAnnotation($method, SignalPriority::class))) {
                    $priorityArgumentName = $priority->argumentName;
                    $priority = MathUtility::forceIntegerInRange((int)$priorityArgumentName, 0, 100);

                    $slots[$priority][] = [
                        'signalClassName' => (string)$signalClassAnnotation->argumentName,
                        'signalName' => (string)$priorityArgumentName,
                        'slotClassNameOrObject' => $slotClass,
                        'slotMethodName' => $method->getName(),
                    ];
                }
            }
        }

        return $this->flattenSlotsByPriority($slots);
    }

    /**
     * Flatten slots by prio.
     *
     * @return mixed[]
     */
    public function flattenSlotsByPriority(array $array): array
    {
        krsort($array);
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
    public function loadExtensionTables(Loader $autoLoader, array $loaderInformation): void
    {
    }

    /**
     * Run the loading process for the ext_localconf.php file.
     */
    public function loadExtensionConfiguration(Loader $autoLoader, array $loaderInformation): void
    {
        if (!empty($loaderInformation)) {
            /** @var Dispatcher $signalSlotDispatcher */
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
