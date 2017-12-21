<?php

/**
 * Register the aspect files and create the Xclass.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Hooks;

use HDNET\Autoloader\Utility\ExtendedUtility;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Database\TableConfigurationPostProcessingHookInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Register the aspect files and create the needed Xclasses.
 *
 * @hook TYPO3_CONF_VARS|SC_OPTIONS|GLOBAL|extTablesInclusion-PostProcessing
 */
class RegisterAspect implements TableConfigurationPostProcessingHookInterface
{
    /**
     * The xclass template content.
     *
     * @var string
     */
    protected static $xclassTemplate = false;

    /**
     * Function which may process data created / registered by extTables
     * scripts (f.e. modifying TCA data of all extensions).
     */
    public function processData()
    {
        $aspectCollection = $GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['Aspect'];

        if (!\is_array($aspectCollection)) {
            return;
        }

        $xClasses = $this->prepareConfiguration($aspectCollection);

        $this->loadXclassTemplate();

        /** @var $cache \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend */
        $cache = GeneralUtility::makeInstance(CacheManager::class)
            ->getCache('autoloader');
        foreach ($xClasses as $xClassName => $xClass) {
            // Register the Xclass in TYPO3
            $this->registerXclass($xClassName);

            // get the Cache Identifier
            $cacheIdentifier = $this->getCacheIdentifier($xClassName);
            if (!$cache->has($cacheIdentifier)) {
                $code = $this->generateXclassCode($xClassName, $xClass, self::$xclassTemplate);
                // !! ;) !! in the xclass-template ist a <?php string for better development
                $code = \str_replace('<?php', '', $code);
                $cache->set($cacheIdentifier, $code);
            }
        }
    }

    /**
     * Load the xclass template and cache it in a local property.
     */
    protected function loadXclassTemplate()
    {
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
    protected function generateXclassCode($xClassName, $xClass, $xclassTemplate)
    {
        $shortName = $this->getXclassShortname($xClassName);

        $xclassTemplate = \str_replace('__classname__', $shortName, $xclassTemplate);
        $xclassTemplate = \str_replace('__extendedClass__', '\\' . $xClassName, $xclassTemplate);

        $beforeConfiguration = [];
        $replaceConfiguration = [];
        $afterConfiguration = [];
        $throwConfiguration = [];
        $joinPointMethods = [];

        foreach ($xClass as $joinPoint => $advices) {
            $beforeConfiguration[$joinPoint] = $this->getConfigurationArray('before', $joinPoint, $advices);
            $replaceConfiguration[$joinPoint] = $this->getConfigurationArray('replace', $joinPoint, $advices);
            $afterConfiguration[$joinPoint] = $this->getConfigurationArray('after', $joinPoint, $advices);
            $throwConfiguration[$joinPoint] = $this->getConfigurationArray('throw', $joinPoint, $advices);

            $joinPointMethods[$joinPoint] = $this->getJoinPointMethod($joinPoint, $xClass);
        }

        $search = [
            '__beforeAspectsConfiguration__',
            '__replaceAspectsConfiguration__',
            '__afterAspectsConfiguration__',
            '__throwAspectsConfiguration__',
            '__joinPointMethods__',
        ];
        $replace = [
            $this->mergeConfigurationArrayForCode($beforeConfiguration),
            $this->mergeConfigurationArrayForCode($replaceConfiguration),
            $this->mergeConfigurationArrayForCode($afterConfiguration),
            $this->mergeConfigurationArrayForCode($throwConfiguration),
            \implode("\n", $joinPointMethods),
        ];

        return \str_replace($search, $replace, $xclassTemplate);
    }

    /**
     * Return the JoinPoint method.
     *
     * @param string $joinPoint
     * @param array  $xClass
     *
     * @return string
     */
    protected function getJoinPointMethod($joinPoint, $xClass)
    {
        $config = $xClass[$joinPoint];
        $argumentBlock = [];
        $code = [];

        $code[] = 'public function ' . $joinPoint . '(';

        // arguments
        if (\is_array($config['arguments']) && \count($config['arguments']) > 0) {
            $args = [];
            foreach ($config['arguments'] as $arguments) {
                $type = '';
                $reference = '';
                if (null !== $arguments['typeHint'] || '' !== $arguments['typeHint']) {
                    $type = $arguments['typeHint'] . ' ';
                }
                if ($arguments['reference']) {
                    $reference = '&';
                }
                $args[] = $type . $reference . '$' . $arguments['name'];
                $argumentBlock[] = $reference . '$' . $arguments['name'];
            }

            $code[] = \implode(',', $args);
        }

        $code[] = ') {';

        $code[] = '$args = array(';
        $code[] = "\t" . \implode(', ', $argumentBlock);
        $code[] = ');';
        $code[] = 'return $this->aspectLogic(\'' . $joinPoint . '\', $args);';
        $code[] = '}';

        return \implode("\n", $code);
    }

    /**
     * Creates code for a configuration array like:
     * array(
     *  {joinPoint} => array(
     *          {method1}, {method2}, {method3}
     *      )
     * ).
     *
     * @param string $type      before, throw, after, replace
     * @param string $joinPoint
     * @param array  $advices
     *
     * @return string
     */
    protected function getConfigurationArray($type, $joinPoint, $advices)
    {
        $code = [];

        if (!$advices[$type]) {
            return '';
        }

        $code[] = '\'' . $joinPoint . '\' => array(';
        foreach ($advices[$type] as $method) {
            $code[] = 'array(';
            $code[] = '\'id\' => \'' . GeneralUtility::shortMD5(
                $method['aspectClassName'] . $method['aspectMethodName'],
                13
            ) . '\',';
            $code[] = '\'class\' => \'' . $method['aspectClassName'] . '\',';
            $code[] = '\'function\' => \'' . $method['aspectMethodName'] . '\',';
            $code[] = '),';
        }
        $code[] = '),';

        return \implode(LF, $code);
    }

    /**
     * Merge the configuration array for code.
     *
     * @param array $configuration
     *
     * @return string
     */
    protected function mergeConfigurationArrayForCode($configuration)
    {
        $code = [];
        $code[] = 'array(';
        $code[] = \implode("\n", $configuration);
        $code[] = ')';

        return \implode(LF, $code);
    }

    /**
     * Register the Xclass in the TYPO3_CONF_VARS.
     *
     * @param string $xClassName
     */
    protected function registerXclass($xClassName)
    {
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
    protected function getXclassShortname($xClassName)
    {
        $classNameArray = \explode('\\', $xClassName);

        return \array_pop($classNameArray);
    }

    /**
     * Return the Cache identifier.
     *
     * @param string $xClassName
     *
     * @return string
     */
    protected function getCacheIdentifier($xClassName)
    {
        $shortName = $this->getXclassShortname($xClassName);

        return 'XCLASS_' . \str_replace('\\', '', 'HDNET\\Autoloader\\Xclass\\' . $shortName);
    }

    /**
     * Prepare the Configuration.
     *
     * @param array $aspectCollection
     *
     * @return array
     */
    protected function prepareConfiguration(array $aspectCollection)
    {
        $xClasses = [];
        foreach ($aspectCollection as $aspects) {
            foreach ($aspects as $aspect) {
                if (!\array_key_exists($aspect['aspectClassName'], $xClasses)) {
                    $xClasses[$aspect['aspectClassName']] = [];
                }

                if (!\array_key_exists($aspect['aspectJoinPoint'], $xClasses[$aspect['aspectClassName']])) {
                    $xClasses[$aspect['aspectClassName']][$aspect['aspectJoinPoint']] = [];
                    $xClasses[$aspect['aspectClassName']][$aspect['aspectJoinPoint']]['arguments'] = $aspect['aspectJoinPointArguments'];
                }

                if (!\array_key_exists(
                    $aspect['aspectAdvice'],
                    $xClasses[$aspect['aspectClassName']][$aspect['aspectJoinPoint']]
                )
                ) {
                    $xClasses[$aspect['aspectClassName']][$aspect['aspectJoinPoint']][$aspect['aspectAdvice']] = [];
                }

                $xClasses[$aspect['aspectClassName']][$aspect['aspectJoinPoint']][$aspect['aspectAdvice']][] = [
                    'aspectClassName' => $aspect['aspectClassName'],
                    'aspectMethodName' => $aspect['aspectMethodName'],
                ];
            }
        }

        return $xClasses;
    }
}
