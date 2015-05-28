<?php
/**
 * Loading Slots
 *
 * @category Extension
 * @package  Autoloader\Loader
 * @author   Tim Lochmüller
 */

namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\SmartObjectRegister;
use HDNET\Autoloader\Utility\FileUtility;
use HDNET\Autoloader\Utility\IconUtility;
use HDNET\Autoloader\Utility\ReflectionUtility;
use HDNET\Autoloader\Utility\TranslateUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Reflection\PropertyReflection;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

/**
 * Loading Slots
 *
 * @author Tim Lochmüller
 */
class ContentObjects implements LoaderInterface {

	/**
	 * Prepare the content object loader
	 *
	 * @param Loader $loader
	 * @param int    $type
	 *
	 * @return array
	 */
	public function prepareLoader(Loader $loader, $type) {
		$loaderInformation = array();

		$modelPath = ExtensionManagementUtility::extPath($loader->getExtensionKey()) . 'Classes/Domain/Model/Content/';
		$models = FileUtility::getBaseFilesInDir($modelPath, 'php');
		if ($models) {
			TranslateUtility::assureLabel('tt_content.' . $loader->getExtensionKey() . '.header', $loader->getExtensionKey(), $loader->getExtensionKey() . ' (Header)', NULL, 'xml');
		}
		foreach ($models as $model) {
			$key = GeneralUtility::camelCaseToLowerCaseUnderscored($model);
			$className = $loader->getVendorName() . '\\' . ucfirst(GeneralUtility::underscoredToUpperCamelCase($loader->getExtensionKey())) . '\\Domain\\Model\\Content\\' . $model;
			if (!$loader->isInstantiableClass($className)) {
				continue;
			}
			$fieldConfiguration = array();

			// create labels in the ext_tables run, to have a valid DatabaseConnection
			if ($type === LoaderInterface::EXT_TABLES) {
				TranslateUtility::assureLabel('tt_content.' . $key, $loader->getExtensionKey(), $key . ' (Title)', NULL, 'xml');
				TranslateUtility::assureLabel('tt_content.' . $key . '.description', $loader->getExtensionKey(), $key . ' (Description)', NULL, 'xml');
				$fieldConfiguration = $this->getClassPropertiesInLowerCaseUnderscored($className);
				$defaultFields = $this->getDefaultTcaFields();
				$fieldConfiguration = array_diff($fieldConfiguration, $defaultFields);

				// RTE manipulation
				$classReflection = ReflectionUtility::createReflectionClass($className);
				foreach ($classReflection->getProperties() as $property) {
					/** @var $property PropertyReflection */
					if ($property->isTaggedWith('enableRichText')) {
						$search = array_search($property->getName(), $fieldConfiguration);
						if ($search !== FALSE) {
							$fieldConfiguration[$search] .= ';;;richtext:rte_transform[flag=rte_enabled|mode=ts_css]';
						}
					}
				}
			}

			$entry = array(
				'fieldConfiguration' => implode(',', $fieldConfiguration),
				'modelClass'         => $className,
				'model'              => $model,
				'icon'               => IconUtility::getByExtensionKey($loader->getExtensionKey()),
				'noHeader'           => $this->isTaggedWithNoHeader($className),
				'tabInformation'     => ReflectionUtility::getFirstTagValue($className, 'wizardTab')
			);

			SmartObjectRegister::register($entry['modelClass']);
			$loaderInformation[$key] = $entry;
		}

		$this->checkAndCreateDummyTemplates($loaderInformation, $loader);

		return $loaderInformation;
	}

	/**
	 * Check if the class is tagged with noHeader
	 *
	 * @param $class
	 *
	 * @return bool
	 */
	protected function isTaggedWithNoHeader($class) {
		$classReflection = ReflectionUtility::createReflectionClass($class);
		return $classReflection->isTaggedWith('noHeader');
	}

	/**
	 * Check if the templates are exist and create a dummy, if there
	 * is no valid template
	 *
	 * @param array  $loaderInformation
	 * @param Loader $loader
	 */
	protected function checkAndCreateDummyTemplates(array $loaderInformation, Loader $loader) {
		$siteRelPath = ExtensionManagementUtility::siteRelPath($loader->getExtensionKey());
		foreach ($loaderInformation as $configuration) {
			$templatePath = $siteRelPath . 'Resources/Private/Templates/Content/' . $configuration['model'] . '.html';
			$absoluteTemplatePath = GeneralUtility::getFileAbsFileName($templatePath);
			if (!is_file($absoluteTemplatePath)) {
				$beTemplatePath = $siteRelPath . 'Resources/Private/Templates/Content/' . $configuration['model'] . 'Backend.html';
				$absoluteBeTemplatePath = GeneralUtility::getFileAbsFileName($beTemplatePath);

				$templateContent = 'Use object to get access to your domain model: <f:debug>{object}</f:debug>';
				FileUtility::writeFileAndCreateFolder($absoluteTemplatePath, $templateContent);

				$beTemplateContent = 'The ContentObject Preview is configurable in the ContentObject Backend Template.<br />
<code>File: ' . $beTemplatePath . '</code><br />
<strong>Alternative you can delete this file to go back to the old behavior.</strong><br />';
				FileUtility::writeFileAndCreateFolder($absoluteBeTemplatePath, $beTemplateContent);
			}
		}
	}

	/**
	 * Same as getClassProperties, but the fields are in LowerCaseUnderscored
	 *
	 * @param $className
	 *
	 * @return array
	 */
	protected function getClassPropertiesInLowerCaseUnderscored($className) {
		return array_map(function ($value) {
			return GeneralUtility::camelCaseToLowerCaseUnderscored($value);
		}, ReflectionUtility::getDeclaringProperties($className));
	}

	/**
	 * Wrap the given field configuration in the CE default TCA fields
	 *
	 * @param string $configuration
	 *
	 * @return string
	 */
	protected function wrapDefaultTcaConfiguration($configuration) {
		$configuration = trim($configuration) ? trim($configuration) . ',' : '';
		return '--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.general;general,
    --palette--;LLL:EXT:cms/locallang_ttc.xml:palette.header;header,
    --div--;LLL:EXT:autoloader/Resources/Private/Language/locallang.xml:contentData,
    ' . $configuration . '
    --div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
    --palette--;LLL:EXT:cms/locallang_ttc.xml:palette.visibility;visibility,
    --palette--;LLL:EXT:cms/locallang_ttc.xml:palette.access;access,
    --div--;LLL:EXT:cms/locallang_ttc.xml:tabs.extended';
	}

	/**
	 * Get the fields that are in the default configuration
	 *
	 * @param null|string $configuration
	 *
	 * @return array
	 */
	protected function getDefaultTcaFields($configuration = NULL) {
		if ($configuration === NULL) {
			$configuration = $this->wrapDefaultTcaConfiguration('');
		}
		$defaultFields = array();
		$existingFields = array_keys($GLOBALS['TCA']['tt_content']['columns']);
		$parts = GeneralUtility::trimExplode(',', $configuration, TRUE);
		foreach ($parts as $fieldConfiguration) {
			$fieldConfiguration = GeneralUtility::trimExplode(';', $fieldConfiguration, TRUE);
			if (in_array($fieldConfiguration[0], $existingFields)) {
				$defaultFields[] = $fieldConfiguration[0];
			} elseif ($fieldConfiguration[0] == '--palette--') {
				$paletteConfiguration = $GLOBALS['TCA']['tt_content']['palettes'][$fieldConfiguration[2]]['showitem'];
				if (is_string($paletteConfiguration)) {
					$defaultFields = array_merge($defaultFields, $this->getDefaultTcaFields($paletteConfiguration));
				}
			}
		}
		return $defaultFields;
	}

	/**
	 * Run the loading process for the ext_tables.php file
	 *
	 * @param Loader $loader
	 * @param array  $loaderInformation
	 *
	 * @return NULL
	 */
	public function loadExtensionTables(Loader $loader, array $loaderInformation) {
		// content register
		foreach ($loaderInformation as $e => $config) {
			SmartObjectRegister::register($config['modelClass']);

			ExtensionManagementUtility::addPlugin(array(
				TranslateUtility::getLllOrHelpMessage('tt_content.' . $e, $loader->getExtensionKey()),
				$loader->getExtensionKey() . '_' . $e
			), 'CType');

			$typeKey = $loader->getExtensionKey() . '_' . $e;
			if (!isset($GLOBALS['TCA']['tt_content']['types'][$typeKey]['showitem'])) {
				$baseTcaConfiguration = $this->wrapDefaultTcaConfiguration($config['fieldConfiguration']);

				if (ExtensionManagementUtility::isLoaded('gridelements')) {
					$baseTcaConfiguration .= ',tx_gridelements_container,tx_gridelements_columns';
				}

				$GLOBALS['TCA']['tt_content']['types'][$typeKey]['showitem'] = $baseTcaConfiguration;
			}

			$tabName = $config['tabInformation'] ? $config['tabInformation'] : $loader->getExtensionKey();
			ExtensionManagementUtility::addPageTSConfig('
mod.wizards.newContentElement.wizardItems.' . $tabName . '.elements.' . $loader->getExtensionKey() . '_' . $e . ' {
    icon = ' . IconUtility::getByExtensionKey($loader->getExtensionKey()) . '
    title = ' . TranslateUtility::getLllOrHelpMessage('tt_content.' . $e, $loader->getExtensionKey()) . '
    description = ' . TranslateUtility::getLllOrHelpMessage('tt_content.' . $e . '.description', $loader->getExtensionKey()) . '
    tt_content_defValues {
        CType = ' . $loader->getExtensionKey() . '_' . $e . '
    }
}
mod.wizards.newContentElement.wizardItems.' . $tabName . '.show := addToList(' . $loader->getExtensionKey() . '_' . $e . ')');
			$cObjectConfiguration = array(
				'extensionKey'        => $loader->getExtensionKey(),
				'backendTemplatePath' => 'EXT:' . $loader->getExtensionKey() . '/Resources/Private/Templates/Content/' . $config['model'] . 'Backend.html',
				'modelClass'          => $config['modelClass']
			);

			$GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['ContentObject'][$loader->getExtensionKey() . '_' . GeneralUtility::camelCaseToLowerCaseUnderscored($config['model'])] = $cObjectConfiguration;
		}

		if ($loaderInformation) {
			ExtensionManagementUtility::addPageTSConfig('
mod.wizards.newContentElement.wizardItems.' . $loader->getExtensionKey() . ' {
	show = *
	header = ' . TranslateUtility::getLllOrHelpMessage('tt_content.' . $loader->getExtensionKey() . '.header', $loader->getExtensionKey()) . '
}');
		}

		return NULL;
	}

	/**
	 * Run the loading process for the ext_localconf.php file
	 *
	 * @param Loader $loader
	 * @param array  $loaderInformation
	 *
	 * @return NULL
	 */
	public function loadExtensionConfiguration(Loader $loader, array $loaderInformation) {
		static $loadPlugin = TRUE;
		$csc = ExtensionManagementUtility::isLoaded('css_styled_content');
		$typoScript = '';

		if ($loadPlugin) {
			$loadPlugin = FALSE;
			ExtensionUtility::configurePlugin('HDNET.autoloader', 'Content', array('Content' => 'index'), array('Content' => ''));
			if (!$csc) {
				$typoScript .= 'tt_content = CASE
tt_content.key.field = CType';
			}
		}
		foreach ($loaderInformation as $e => $config) {
			$typoScript .= '
        tt_content.' . $loader->getExtensionKey() . '_' . $e . ' = COA
        tt_content.' . $loader->getExtensionKey() . '_' . $e . ' {
            ' . ($config['noHeader'] ? '' : '10 =< lib.stdheader') . '
            20 = USER
            20 {
                userFunc = TYPO3\CMS\Extbase\Core\Bootstrap->run
                extensionName = Autoloader
                pluginName = Content
                vendorName = HDNET
                settings {
	                contentElement = ' . $config['model'] . '
	                extensionKey = ' . $loader->getExtensionKey() . '
	                vendorName = ' . $loader->getVendorName() . '
	            }
            }
        }



                config.tx_extbase.persistence.classes.' . $config['modelClass'] . '.mapping.tableName = tt_content';
		}

		if ($csc) {
			ExtensionManagementUtility::addTypoScript($loader->getExtensionKey(), 'setup', $typoScript, 43);
		} else {
			ExtensionManagementUtility::addTypoScriptSetup($typoScript);
		}

		return NULL;
	}
}