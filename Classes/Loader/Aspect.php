<?php
/**
 * Loading Aspect
 *
 * @category Extension
 * @package  Autoloader\Loader
 * @author   Carsten Biebricher
 */

namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Autoload\TempClassLoader;
use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\Utility\FileUtility;
use HDNET\Autoloader\Utility\ReflectionUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Loading Aspect
 *
 * Aspects available: before, replace, after, throw
 * Used Tags: @aspectClass, @aspectJoinPoint, @aspectAdvice
 *
 * @author Carsten Biebricher
 */
class Aspect implements LoaderInterface {

	/**
	 * Get all the complex data for the loader.
	 * This return value will be cached and stored in the database
	 * There is no file monitoring for this cache
	 *
	 * @param Loader $loader
	 * @param int    $type
	 *
	 * @return array $loaderInformation
	 */
	public function prepareLoader(Loader $loader, $type) {
		$aspects = array();
		$aspectPath = ExtensionManagementUtility::extPath($loader->getExtensionKey()) . 'Classes/Aspect/';
		$aspectClasses = FileUtility::getBaseFilesInDir($aspectPath, 'php');
		$extKey = GeneralUtility::underscoredToUpperCamelCase($loader->getExtensionKey());

		foreach ($aspectClasses as $aspect) {
			$aspectClass = $loader->getVendorName() . '\\' . $extKey . '\\Aspect\\' . $aspect;

			if (!$loader->isInstantiableClass($aspectClass)) {
				continue;
			}

			try {
				$methods = ReflectionUtility::getPublicMethods($aspectClass);
				foreach ($methods as $methodReflection) {
					/** @var $methodReflection \TYPO3\CMS\Extbase\Reflection\MethodReflection */
					$methodTags = $methodReflection->getTagsValues();

					if (isset($methodTags['aspectClass'][0]) && isset($methodTags['aspectJoinPoint'][0]) && isset($methodTags['aspectAdvice'][0])) {
						$aspectClassName = trim($methodTags['aspectClass'][0], '\\');
						$aspectJoinPoint = trim($methodTags['aspectJoinPoint'][0]);

						// check only if class exists
						if (!$loader->isInstantiableClass($aspectClassName)) {
							continue;
						}

						$aspectJpArguments = $this->getMethodArgumentsFromClassMethod($aspectClassName, $aspectJoinPoint);

						$aspects[] = array(
							'aspectClassName'          => $aspectClassName,
							'aspectJoinPoint'          => $aspectJoinPoint,
							'aspectJoinPointArguments' => $aspectJpArguments,
							'aspectAdvice'             => trim($methodTags['aspectAdvice'][0]),
							'originClassName'          => $aspectClass,
							'originMethodName'         => $methodReflection->getName()
						);
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
	protected function getMethodArgumentsFromClassMethod($aspectClassName, $aspectJoinPoint) {
		$reflectionClass = ReflectionUtility::createReflectionClass($aspectClassName);
		$methodReflection = $reflectionClass->getMethod($aspectJoinPoint);

		/** @var $classReflection \TYPO3\CMS\Extbase\Reflection\ClassReflection */
		$methodArguments = $methodReflection->getParameters();
		$arguments = array();
		/** @var $argument \ReflectionParameter */
		foreach ($methodArguments as $argument) {
			$arguments[] = array(
				'name'      => $argument->getName(),
				'typeHint'  => $argument->getClass()->name,
				'reference' => $argument->isPassedByReference()
			);
		}

		return $arguments;
	}

	/**
	 * Run the loading process for the ext_tables.php file.
	 *
	 * @param Loader $loader
	 * @param array  $loaderInformation
	 *
	 * @return NULL
	 */
	public function loadExtensionTables(Loader $loader, array $loaderInformation) {
		return NULL;
	}

	/**
	 * Run the loading process for the ext_localconf.php file.
	 *
	 * @param Loader $loader
	 * @param array  $loaderInformation
	 *
	 * @return NULL
	 */
	public function loadExtensionConfiguration(Loader $loader, array $loaderInformation) {
		if ($loaderInformation) {
			TempClassLoader::registerAutoloader();
			$GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['Aspect'][] = $loaderInformation;
		}

		return NULL;
	}
}