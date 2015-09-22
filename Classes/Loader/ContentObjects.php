<?php
/**
 * Loading Slots
 *
 * @author Tim Lochmüller
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
 */
class ContentObjects implements LoaderInterface
{

    /**
     * Prepare the content object loader
     *
     * @param Loader $loader
     * @param int    $type
     *
     * @return array
     */
    public function prepareLoader(Loader $loader, $type)
    {
        $loaderInformation = [];

        $modelPath = ExtensionManagementUtility::extPath($loader->getExtensionKey()) . 'Classes/Domain/Model/Content/';
        $models = FileUtility::getBaseFilesInDir($modelPath, 'php');
        if ($models) {
            TranslateUtility::assureLabel('tt_content.' . $loader->getExtensionKey() . '.header', $loader->getExtensionKey(),
                $loader->getExtensionKey() . ' (Header)');
        }
        foreach ($models as $model) {
            $key = GeneralUtility::camelCaseToLowerCaseUnderscored($model);
            $className = $loader->getVendorName() . '\\' . ucfirst(GeneralUtility::underscoredToUpperCamelCase($loader->getExtensionKey())) . '\\Domain\\Model\\Content\\' . $model;
            if (!$loader->isInstantiableClass($className)) {
                continue;
            }
            $fieldConfiguration = [];

            // create labels in the ext_tables run, to have a valid DatabaseConnection
            if ($type === LoaderInterface::EXT_TABLES) {
                TranslateUtility::assureLabel('tt_content.' . $key, $loader->getExtensionKey(), $key . ' (Title)');
                TranslateUtility::assureLabel('tt_content.' . $key . '.description', $loader->getExtensionKey(),
                    $key . ' (Description)');
                $fieldConfiguration = $this->getClassPropertiesInLowerCaseUnderscored($className);
                $defaultFields = $this->getDefaultTcaFields();
                $fieldConfiguration = array_diff($fieldConfiguration, $defaultFields);

                // RTE manipulation
                $classReflection = ReflectionUtility::createReflectionClass($className);
                foreach ($classReflection->getProperties() as $property) {
                    /** @var $property PropertyReflection */
                    if ($property->isTaggedWith('enableRichText')) {
                        $search = array_search($property->getName(), $fieldConfiguration);
                        if ($search !== false) {
                            $fieldConfiguration[$search] .= ';;;richtext:rte_transform[flag=rte_enabled|mode=ts_css]';
                        }
                    }
                }
            }

            $entry = [
                'fieldConfiguration' => implode(',', $fieldConfiguration),
                'modelClass'         => $className,
                'model'              => $model,
                'icon'               => IconUtility::getByExtensionKey($loader->getExtensionKey()),
                'noHeader'           => $this->isTaggedWithNoHeader($className),
                'tabInformation'     => ReflectionUtility::getFirstTagValue($className, 'wizardTab')
            ];

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
    protected function isTaggedWithNoHeader($class)
    {
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
    protected function checkAndCreateDummyTemplates(array $loaderInformation, Loader $loader)
    {
        $siteRelPathPrivate = ExtensionManagementUtility::siteRelPath($loader->getExtensionKey()) . 'Resources/Private/';
        $frontendLayout = GeneralUtility::getFileAbsFileName($siteRelPathPrivate . 'Layouts/Content.html');
        if (!file_exists($frontendLayout)) {
            $this->writeContentTemplateToTarget('FrontendLayout', $frontendLayout);
        }
        $backendLayout = GeneralUtility::getFileAbsFileName($siteRelPathPrivate . 'Layouts/ContentBackend.html');
        if (!file_exists($backendLayout)) {
            $this->writeContentTemplateToTarget('BackendLayout', $backendLayout);
        }

        foreach ($loaderInformation as $configuration) {
            $templatePath = $siteRelPathPrivate . 'Templates/Content/' . $configuration['model'] . '.html';
            $absoluteTemplatePath = GeneralUtility::getFileAbsFileName($templatePath);
            if (!is_file($absoluteTemplatePath)) {
                $beTemplatePath = $siteRelPathPrivate . 'Templates/Content/' . $configuration['model'] . 'Backend.html';
                $absoluteBeTemplatePath = GeneralUtility::getFileAbsFileName($beTemplatePath);

                $this->writeContentTemplateToTarget('Frontend', $absoluteTemplatePath);
                $this->writeContentTemplateToTarget('Backend', $absoluteBeTemplatePath);
            }
        }
    }

    /**
     * Write the given content object template to the target path
     *
     * @param string $name
     * @param string $absoluteTargetPath
     */
    protected function writeContentTemplateToTarget($name, $absoluteTargetPath)
    {
        $template = GeneralUtility::getUrl(ExtensionManagementUtility::extPath('autoloader',
            'Resources/Private/Templates/ContentObjects/' . $name . '.html'));
        FileUtility::writeFileAndCreateFolder($absoluteTargetPath, $template);
    }

    /**
     * Same as getClassProperties, but the fields are in LowerCaseUnderscored
     *
     * @param $className
     *
     * @return array
     */
    protected function getClassPropertiesInLowerCaseUnderscored($className)
    {
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
    protected function wrapDefaultTcaConfiguration($configuration)
    {
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
    protected function getDefaultTcaFields($configuration = null)
    {
        if ($configuration === null) {
            $configuration = $this->wrapDefaultTcaConfiguration('');
        }
        $defaultFields = [];
        $existingFields = array_keys($GLOBALS['TCA']['tt_content']['columns']);
        $parts = GeneralUtility::trimExplode(',', $configuration, true);
        foreach ($parts as $fieldConfiguration) {
            $fieldConfiguration = GeneralUtility::trimExplode(';', $fieldConfiguration, true);
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
    public function loadExtensionTables(Loader $loader, array $loaderInformation)
    {
        $createWizardHeader = [];
        $predefinedWizards = [
            'common',
            'special',
            'forms',
            'plugins',
        ];

        foreach ($loaderInformation as $e => $config) {
            SmartObjectRegister::register($config['modelClass']);

            ExtensionManagementUtility::addPlugin([
                TranslateUtility::getLllOrHelpMessage('content.element.' . $e, $loader->getExtensionKey()),
                $loader->getExtensionKey() . '_' . $e
            ], 'CType');

            $typeKey = $loader->getExtensionKey() . '_' . $e;
            if (!isset($GLOBALS['TCA']['tt_content']['types'][$typeKey]['showitem'])) {
                $baseTcaConfiguration = $this->wrapDefaultTcaConfiguration($config['fieldConfiguration']);

                if (ExtensionManagementUtility::isLoaded('gridelements')) {
                    $baseTcaConfiguration .= ',tx_gridelements_container,tx_gridelements_columns';
                }

                $GLOBALS['TCA']['tt_content']['types'][$typeKey]['showitem'] = $baseTcaConfiguration;
            }

            $tabName = $config['tabInformation'] ? $config['tabInformation'] : $loader->getExtensionKey();
            if (!in_array($tabName, $predefinedWizards) && !in_array($tabName, $createWizardHeader)) {
                $createWizardHeader[] = $tabName;
            }
            ExtensionManagementUtility::addPageTSConfig('
mod.wizards.newContentElement.wizardItems.' . $tabName . '.elements.' . $loader->getExtensionKey() . '_' . $e . ' {
    icon = ' . IconUtility::getByModelName($config['modelClass']) . '
    title = ' . TranslateUtility::getLllOrHelpMessage('wizard.' . $e, $loader->getExtensionKey()) . '
    description = ' . TranslateUtility::getLllOrHelpMessage('wizard.' . $e . '.description', $loader->getExtensionKey()) . '
    tt_content_defValues {
        CType = ' . $loader->getExtensionKey() . '_' . $e . '
    }
}
mod.wizards.newContentElement.wizardItems.' . $tabName . '.show := addToList(' . $loader->getExtensionKey() . '_' . $e . ')');
            $cObjectConfiguration = [
                'extensionKey'        => $loader->getExtensionKey(),
                'backendTemplatePath' => 'EXT:' . $loader->getExtensionKey() . '/Resources/Private/Templates/Content/' . $config['model'] . 'Backend.html',
                'modelClass'          => $config['modelClass']
            ];

            $GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['ContentObject'][$loader->getExtensionKey() . '_' . GeneralUtility::camelCaseToLowerCaseUnderscored($config['model'])] = $cObjectConfiguration;
        }

        if ($createWizardHeader) {
            foreach ($createWizardHeader as $element) {
                ExtensionManagementUtility::addPageTSConfig('
mod.wizards.newContentElement.wizardItems.' . $element . ' {
	show = *
	header = ' . TranslateUtility::getLllOrHelpMessage('wizard.' . $element . '.header', $loader->getExtensionKey()) . '
}');
            }
        }

        return null;
    }

    /**
     * Run the loading process for the ext_localconf.php file
     *
     * @param Loader $loader
     * @param array  $loaderInformation
     *
     * @return NULL
     */
    public function loadExtensionConfiguration(Loader $loader, array $loaderInformation)
    {
        static $loadPlugin = true;
        $csc = ExtensionManagementUtility::isLoaded('css_styled_content');
        $typoScript = '';

        if ($loadPlugin) {
            $loadPlugin = false;
            ExtensionUtility::configurePlugin('HDNET.autoloader', 'Content', ['Content' => 'index'], ['Content' => '']);
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

        return null;
    }
}