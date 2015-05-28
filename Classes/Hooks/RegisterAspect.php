<?php
/**
 * Register the aspect files and create the Xclass.
 *
 * @category Extension
 * @package  Autoloader
 * @author   Carsten Biebricher
 */

namespace HDNET\Autoloader\Hooks;

use HDNET\Autoloader\Utility\ExtendedUtility;
use TYPO3\CMS\Core\Database\TableConfigurationPostProcessingHookInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Register the aspect files and create the needed Xclasses.
 *
 * @author Carsten Biebricher
 * @hook   TYPO3_CONF_VARS|SC_OPTIONS|GLOBAL|extTablesInclusion-PostProcessing
 */
class RegisterAspect implements TableConfigurationPostProcessingHookInterface {

	/**
	 * The xclass template content
	 *
	 * @var string
	 */
	static protected $xclassTemplate = FALSE;

	/**
	 * Function which may process data created / registered by extTables
	 * scripts (f.e. modifying TCA data of all extensions)
	 *
	 * @return void
	 */
	public function processData() {
		$aspectCollection = $GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['Aspect'];

		if (!is_array($aspectCollection)) {
			return;
		}

		$xClasses = $this->prepareConfiguration($aspectCollection);

		$this->loadXclassTemplate();

		/** @var $cache \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend */
		$cache = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheManager')
			->getCache('autoloader');
		foreach ($xClasses as $xClassName => $xClass) {
			// Register the Xclass in TYPO3
			$this->registerXclass($xClassName);

			// get the Cache Identifier
			$cacheIdentifier = $this->getCacheIdentifier($xClassName);
			if (!$cache->has($cacheIdentifier)) {
				$code = $this->generateXclassCode($xClassName, $xClass, self::$xclassTemplate);
				// !! ;) !! in the xclass-template ist a <?php string for better development
				$code = str_replace('<?php', '', $code);
				$cache->set($cacheIdentifier, $code);
			}
		}
	}

	/**
	 * Load the xclass template and cache it in a local property
	 *
	 * @return null
	 */
	protected function loadXclassTemplate() {
		if (!self::$xclassTemplate) {
			$xclassTemplatePath = ExtensionManagementUtility::extPath('autoloader') . 'Resources/Private/Templates/Xclass/Aspect.tmpl';
			self::$xclassTemplate = GeneralUtility::getUrl($xclassTemplatePath);
		}
	}

	/**
	 * Generate the Xclass code on base of the xclass-template.
	 *
	 * @param string $xClassName
	 * @param array  $xClass
	 * @param string $xclassTemplate
	 *
	 * @return string full xclass-code
	 */
	protected function generateXclassCode($xClassName, $xClass, $xclassTemplate) {
		$shortName = $this->getXclassShortname($xClassName);

		$xclassTemplate = str_replace('__classname__', $shortName, $xclassTemplate);
		$xclassTemplate = str_replace('__extendedClass__', '\\' . $xClassName, $xclassTemplate);

		$beforeConfiguration = array();
		$replaceConfiguration = array();
		$afterConfiguration = array();
		$throwConfiguration = array();
		$joinPointMethods = array();

		foreach ($xClass as $joinPoint => $advices) {
			$beforeConfiguration[$joinPoint] = $this->getConfigurationArray('before', $joinPoint, $advices);
			$replaceConfiguration[$joinPoint] = $this->getConfigurationArray('replace', $joinPoint, $advices);
			$afterConfiguration[$joinPoint] = $this->getConfigurationArray('after', $joinPoint, $advices);
			$throwConfiguration[$joinPoint] = $this->getConfigurationArray('throw', $joinPoint, $advices);

			$joinPointMethods[$joinPoint] = $this->getJoinPointMethod($joinPoint, $xClass);
		}

		$search = array(
			'__beforeAspectsConfiguration__',
			'__replaceAspectsConfiguration__',
			'__afterAspectsConfiguration__',
			'__throwAspectsConfiguration__',
			'__joinPointMethods__',
		);
		$replace = array(
			$this->mergeConfigurationArrayForCode($beforeConfiguration),
			$this->mergeConfigurationArrayForCode($replaceConfiguration),
			$this->mergeConfigurationArrayForCode($afterConfiguration),
			$this->mergeConfigurationArrayForCode($throwConfiguration),
			implode("\n", $joinPointMethods),
		);

		return str_replace($search, $replace, $xclassTemplate);
	}

	/**
	 * Return the JoinPoint method.
	 *
	 * @param string $joinPoint
	 * @param array  $xClass
	 *
	 * @return string
	 */
	protected function getJoinPointMethod($joinPoint, $xClass) {
		$config = $xClass[$joinPoint];
		$argumentBlock = array();
		$code = array();

		$code[] = 'public function ' . $joinPoint . '(';

		// arguments
		if (is_array($config['arguments']) && sizeof($config['arguments']) > 0) {
			$args = array();
			foreach ($config['arguments'] as $arguments) {
				$type = '';
				$reference = '';
				if ($arguments['typeHint'] !== NULL || $arguments['typeHint'] !== '') {
					$type = $arguments['typeHint'] . ' ';
				}
				if ($arguments['reference']) {
					$reference = '&';
				}
				$args[] = $type . $reference . '$' . $arguments['name'];
				$argumentBlock[] = $reference . '$' . $arguments['name'];
			}

			$code[] = implode(',', $args);
		}

		$code[] = ') {';

		$code[] = '$args = array(';
		$code[] = "\t" . implode(', ', $argumentBlock);
		$code[] = ');';
		$code[] = 'return $this->aspectLogic(\'' . $joinPoint . '\', $args);';
		$code[] = '}';

		return implode("\n", $code);
	}

	/**
	 * Creates code for a configuration array like:
	 * array(
	 *  {joinPoint} => array(
	 *          {method1}, {method2}, {method3}
	 *      )
	 * )
	 *
	 * @param string $type before, throw, after, replace
	 * @param string $joinPoint
	 * @param array  $advices
	 *
	 * @return string
	 */
	protected function getConfigurationArray($type, $joinPoint, $advices) {
		$code = array();

		if (!$advices[$type]) {
			return '';
		}

		$code[] = '\'' . $joinPoint . '\' => array(';
		foreach ($advices[$type] as $method) {
			$code[] = 'array(';
			$code[] = '\'id\' => \'' . GeneralUtility::shortMD5($method['originClassName'] . $method['originMethodName'], 13) . '\',';
			$code[] = '\'class\' => \'\\' . $method['originClassName'] . '\',';
			$code[] = '\'function\' => \'' . $method['originMethodName'] . '\',';
			$code[] = '),';
		}
		$code[] = '),';

		return implode(LF, $code);
	}

	/**
	 * Merge the configuration array for code
	 *
	 * @param array $configuration
	 *
	 * @return string
	 */
	protected function mergeConfigurationArrayForCode($configuration) {
		$code[] = 'array(';
		$code[] = implode("\n", $configuration);
		$code[] = ')';

		return implode(LF, $code);
	}

	/**
	 * Register the Xclass in the TYPO3_CONF_VARS.
	 *
	 * @param string $xClassName
	 */
	protected function registerXclass($xClassName) {
		// Register the Xclass in TYPO3
		$shortName = $this->getXclassShortname($xClassName);
		$loaderClassName = 'HDNET\\Autoloader\\Xclass\\' . $shortName;
		ExtendedUtility::addXclass($xClassName, $loaderClassName);
	}

	/**
	 * Return from the full namespace the classname.
	 *
	 * @param string $xClassName
	 *
	 * @return string
	 */
	protected function getXclassShortname($xClassName) {
		$classNameArray = explode('\\', $xClassName);
		$shortName = array_pop($classNameArray);

		return $shortName;
	}

	/**
	 * Return the Cache identifier.
	 *
	 * @param string $xClassName
	 *
	 * @return string
	 */
	protected function getCacheIdentifier($xClassName) {
		$shortName = $this->getXclassShortname($xClassName);
		return 'XCLASS_' . str_replace('\\', '', 'HDNET\\Autoloader\\Xclass\\' . $shortName);
	}

	/**
	 * Prepare the Configuration.
	 *
	 * @param array $aspectCollection
	 *
	 * @return array
	 */
	protected function prepareConfiguration(array $aspectCollection) {
		$xClasses = array();
		foreach ($aspectCollection as $aspects) {
			foreach ($aspects as $aspect) {
				if (!array_key_exists($aspect['aspectClassName'], $xClasses)) {
					$xClasses[$aspect['aspectClassName']] = array();
				}

				if (!array_key_exists($aspect['aspectJoinPoint'], $xClasses[$aspect['aspectClassName']])) {
					$xClasses[$aspect['aspectClassName']][$aspect['aspectJoinPoint']] = array();
					$xClasses[$aspect['aspectClassName']][$aspect['aspectJoinPoint']]['arguments'] = $aspect['aspectJoinPointArguments'];
				}

				if (!array_key_exists($aspect['aspectAdvice'], $xClasses[$aspect['aspectClassName']][$aspect['aspectJoinPoint']])) {
					$xClasses[$aspect['aspectClassName']][$aspect['aspectJoinPoint']][$aspect['aspectAdvice']] = array();
				}

				$xClasses[$aspect['aspectClassName']][$aspect['aspectJoinPoint']][$aspect['aspectAdvice']][] = array(
					'originClassName'  => $aspect['originClassName'],
					'originMethodName' => $aspect['originMethodName']
				);
			}
		}

		return $xClasses;
	}
}