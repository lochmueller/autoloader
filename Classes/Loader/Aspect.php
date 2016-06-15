<?php
/**
 * Loading Aspect
 *
 * @author Carsten Biebricher
 */

namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Autoload\TempClassLoader;
use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\Utility\ClassNamingUtility;
use HDNET\Autoloader\Utility\FileUtility;
use HDNET\Autoloader\Utility\ReflectionUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Loading Aspect
 *
 * Aspects available: before, replace, after, throw
 * Used Tags: @aspectClass, @aspectJoinPoint, @aspectAdvice
 */
class Aspect implements LoaderInterface
{

    /**
     * Get all the complex data for the loader.
     * This return value will be cached and stored in the database
     * There is no file monitoring for this cache
     *
     * @param Loader $loader
     * @param int $type
     *
     * @return array $loaderInformation
     */
    public function prepareLoader(Loader $loader, $type)
    {
        $aspects = [];
        $aspectPath = ExtensionManagementUtility::extPath($loader->getExtensionKey()) . 'Classes/Aspect/';
        $aspectClasses = FileUtility::getBaseFilesInDir($aspectPath, 'php');
        $extKey = GeneralUtility::underscoredToUpperCamelCase($loader->getExtensionKey());

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
                $methods = ReflectionUtility::getPublicMethods($aspectClass);
                foreach ($methods as $methodReflection) {
                    /** @var $methodReflection \TYPO3\CMS\Extbase\Reflection\MethodReflection */
                    $tagConfiguration = ReflectionUtility::getTagConfiguration(
                        $methodReflection,
                        ['aspectClass', 'aspectJoinPoint', 'aspectAdvice']
                    );
                    foreach ($tagConfiguration['aspectClass'] as $key => $aspectClass) {
                        if (!isset($tagConfiguration['aspectJoinPoint'][$key]) || !isset($tagConfiguration['aspectAdvice'][$key])) {
                            continue;
                        }

                        $aspectClassName = trim($aspectClass, '\\');
                        $aspectJoinPoint = trim($tagConfiguration['aspectJoinPoint'][$key]);

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
                            'aspectJoinPoint' => $aspectJoinPoint,
                            'aspectJoinPointArguments' => $aspectJpArguments,
                            'aspectAdvice' => trim($tagConfiguration['aspectAdvice'][$key]),
                            'originClassName' => $aspectClass,
                            'originMethodName' => $methodReflection->getName()
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
     * Get the Arguments from the original method via Reflection.
     * If the $aspectClassName not available (e.g. Extension is not installed) then
     * throw a Exception.
     *
     * @param $aspectClassName
     * @param $aspectJoinPoint
     *
     * @return array
     * @throws \HDNET\Autoloader\Exception
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
                'reference' => $argument->isPassedByReference()
            ];
        }

        return $arguments;
    }

    /**
     * Run the loading process for the ext_tables.php file.
     *
     * @param Loader $loader
     * @param array $loaderInformation
     *
     * @return NULL
     */
    public function loadExtensionTables(Loader $loader, array $loaderInformation)
    {
        return null;
    }

    /**
     * Run the loading process for the ext_localconf.php file.
     *
     * @param Loader $loader
     * @param array $loaderInformation
     *
     * @return NULL
     */
    public function loadExtensionConfiguration(Loader $loader, array $loaderInformation)
    {
        if ($loaderInformation) {
            TempClassLoader::registerAutoloader();
            $GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['Aspect'][] = $loaderInformation;
        }

        return null;
    }
}
