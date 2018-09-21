<?php

/**
 * Loading Aspect.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Autoload\TempClassLoader;
use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\Utility\ClassNamingUtility;
use HDNET\Autoloader\Utility\FileUtility;
use HDNET\Autoloader\Utility\ReflectionUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Loading Aspect.
 *
 * Aspects available: before, replace, after, throw
 * Used Tags: @aspectClass, @aspectJoinPoint, @aspectAdvice
 */
class Aspect implements LoaderInterface
{
    /**
     * Get all the complex data for the loader.
     * This return value will be cached and stored in the database
     * There is no file monitoring for this cache.
     *
     * @param Loader $loader
     * @param int    $type
     *
     * @return array $loaderInformation
     */
    public function prepareLoader(Loader $loader, int $type): array
    {
        $aspects = [];
        $aspectPath = ExtensionManagementUtility::extPath($loader->getExtensionKey()) . 'Classes/Aspect/';
        $aspectClasses = FileUtility::getBaseFilesInDir($aspectPath, 'php');

        foreach ($aspectClasses as $aspect) {
            $aspectClass = ClassNamingUtility::getFqnByPath(
                $loader->getVendorName(),
                $loader->getExtensionKey(),
                'Aspect/' . $aspect
            );

            if (!$loader->isInstantiableClass($aspectClass)) {
                continue;
            }

            try {
                $methods = ReflectionUtility::getPublicMethodNames($aspectClass);
                foreach ($methods as $methodName) {
                    $tagConfiguration = ReflectionUtility::getTagConfigurationForMethod($aspectClass, $methodName,
                        ['aspectClass', 'aspectJoinPoint', 'aspectAdvice']
                    );
                    foreach ($tagConfiguration['aspectClass'] as $key => $aspectClass) {
                        if (!isset($tagConfiguration['aspectJoinPoint'][$key]) || !isset($tagConfiguration['aspectAdvice'][$key])) {
                            continue;
                        }

                        $aspectClassName = \trim($aspectClass, '\\');
                        $aspectJoinPoint = \trim($tagConfiguration['aspectJoinPoint'][$key]);

                        // check only if class exists
                        if (!$loader->isInstantiableClass($aspectClassName)) {
                            continue;
                        }

                        $aspectJpArguments = $this->getMethodArgumentsFromClassMethod(
                            $aspectClassName,
                            $aspectJoinPoint
                        );

                        $aspects[] = [
                            'aspectClassName' => $aspectClassName,
                            'aspectMethodName' => $methodName,
                            'aspectJoinPoint' => $aspectJoinPoint,
                            'aspectJoinPointArguments' => $aspectJpArguments,
                            'aspectAdvice' => \trim($tagConfiguration['aspectAdvice'][$key]),
                        ];
                    }
                }
            } catch (\Exception $e) {
                // Class or file is not available for Aspects $aspectClassName
                continue;
            }
        }

        return $aspects;
    }

    /**
     * Run the loading process for the ext_tables.php file.
     *
     * @param Loader $loader
     * @param array  $loaderInformation
     */
    public function loadExtensionTables(Loader $loader, array $loaderInformation)
    {
    }

    /**
     * Run the loading process for the ext_localconf.php file.
     *
     * @param Loader $loader
     * @param array  $loaderInformation
     */
    public function loadExtensionConfiguration(Loader $loader, array $loaderInformation)
    {
        if (!empty($loaderInformation)) {
            TempClassLoader::registerAutoloader();
            $GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['Aspect'][] = $loaderInformation;
        }
    }

    /**
     * Get the Arguments from the original method via Reflection.
     * If the $aspectClassName not available (e.g. Extension is not installed) then
     * throw a Exception.
     *
     * @param $aspectClassName
     * @param $aspectJoinPoint
     *
     * @throws \HDNET\Autoloader\Exception
     *
     * @return array
     */
    protected function getMethodArgumentsFromClassMethod($aspectClassName, $aspectJoinPoint)
    {
        $reflectionClass = ReflectionUtility::createReflectionClass($aspectClassName);
        $methodReflection = $reflectionClass->getMethod($aspectJoinPoint);

        /** @var $classReflection \TYPO3\CMS\Extbase\Reflection\ClassReflection */
        $methodArguments = $methodReflection->getParameters();
        $arguments = [];
        /** @var $argument \ReflectionParameter */
        foreach ($methodArguments as $argument) {
            $arguments[] = [
                'name' => $argument->getName(),
                'typeHint' => $argument->getClass()->name,
                'reference' => $argument->isPassedByReference(),
            ];
        }

        return $arguments;
    }
}
