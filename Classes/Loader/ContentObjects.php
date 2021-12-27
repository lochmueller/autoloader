<?php

/**
 * Loading Slots.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Loader;

use Doctrine\Common\Annotations\AnnotationReader;
use HDNET\Autoloader\Annotation\EnableRichText;
use HDNET\Autoloader\Annotation\NoHeader;
use HDNET\Autoloader\Annotation\WizardTab;
use HDNET\Autoloader\Controller\ContentController;
use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\Service\NameMapperService;
use HDNET\Autoloader\SmartObjectRegister;
use HDNET\Autoloader\Utility\ClassNamingUtility;
use HDNET\Autoloader\Utility\FileUtility;
use HDNET\Autoloader\Utility\IconUtility;
use HDNET\Autoloader\Utility\ModelUtility;
use HDNET\Autoloader\Utility\ReflectionUtility;
use HDNET\Autoloader\Utility\TranslateUtility;
use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

/**
 * Loading Slots.
 */
class ContentObjects implements LoaderInterface
{
    /**
     * Prepare the content object loader.
     *
     * @return array<string, array<string, mixed>>
     */
    public function prepareLoader(Loader $loader, int $type): array
    {
        $loaderInformation = [];

        /** @var AnnotationReader $annotationReader */
        $annotationReader = GeneralUtility::makeInstance(AnnotationReader::class);

        $modelPath = ExtensionManagementUtility::extPath($loader->getExtensionKey()) . 'Classes/Domain/Model/Content/';
        $models = FileUtility::getBaseFilesInDir($modelPath, 'php');
        if (!empty($models)) {
            TranslateUtility::assureLabel(
                'tt_content.' .
                $loader->getExtensionKey() . '.header',
                $loader->getExtensionKey(),
                $loader->getExtensionKey() . ' (Header)'
            );
        }
        foreach ($models as $model) {
            $key = GeneralUtility::camelCaseToLowerCaseUnderscored($model);
            $className = ClassNamingUtility::getFqnByPath(
                $loader->getVendorName(),
                $loader->getExtensionKey(),
                'Domain/Model/Content/' . $model
            );
            if (!$loader->isInstantiableClass($className)) {
                continue;
            }
            $fieldConfiguration = [];
            $richTextFields = [];
            $noHeader = $this->isTaggedWithNoHeader($className);

            $reflectionClass = new \ReflectionClass($className);

            // create labels in the ext_tables run, to have a valid DatabaseConnection
            if (LoaderInterface::EXT_TABLES === $type) {
                TranslateUtility::assureLabel('wizard.' . $key, $loader->getExtensionKey(), $key . ' (Title)', null, 'tt_content');
                TranslateUtility::assureLabel(
                    'wizard.' . $key . '.description',
                    $loader->getExtensionKey(),
                    $key . ' (Description)',
                    null,
                    'tt_content'
                );
                $fieldConfiguration = $this->getClassPropertiesInLowerCaseUnderscored($className);
                $defaultFields = $this->getDefaultTcaFields($noHeader, null);
                $fieldConfiguration = array_diff($fieldConfiguration, $defaultFields);

                // RTE manipulation
                foreach ($reflectionClass->getProperties() as $property) {
                    $richTextField = $annotationReader->getPropertyAnnotation($property, EnableRichText::class);
                    if (!$richTextField instanceof EnableRichText) {
                        continue;
                    }

                    $search = array_search(
                        GeneralUtility::camelCaseToLowerCaseUnderscored($property->getName()),
                        $fieldConfiguration,
                        true
                    );
                    if (false !== $search) {
                        $richTextFields[] = $fieldConfiguration[$search];
                    }
                }
            }

            $entry = [
                'fieldConfiguration' => implode(',', $fieldConfiguration),
                'richTextFields' => $richTextFields,
                'modelClass' => $className,
                'model' => $model,
                'icon' => IconUtility::getByModelName($className, false),
                'iconExt' => IconUtility::getByModelName($className, true),
                'noHeader' => $noHeader,
                'tabInformation' => (string)$annotationReader->getClassAnnotation($reflectionClass, WizardTab::class),
            ];

            SmartObjectRegister::register($entry['modelClass']);
            $loaderInformation[$key] = $entry;
        }

        $this->checkAndCreateDummyTemplates($loaderInformation, $loader);

        return $loaderInformation;
    }

    /**
     * Run the loading process for the ext_tables.php file.
     */
    public function loadExtensionTables(Loader $loader, array $loaderInformation): void
    {
        if (empty($loaderInformation)) {
            return;
        }
        $createWizardHeader = [];
        $predefinedWizards = [
            'common',
            'special',
            'forms',
            'plugins',
        ];

        // Add the divider
        $GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'][] = [
            TranslateUtility::getLllString(
                'tt_content.' . $loader->getExtensionKey() . '.header',
                $loader->getExtensionKey(),
                null,
                'tt_content'
            ),
            '--div--',
        ];

        foreach ($loaderInformation as $e => $config) {
            SmartObjectRegister::register($config['modelClass']);
            $typeKey = $loader->getExtensionKey() . '_' . $e;

            ExtensionManagementUtility::addPlugin([
                TranslateUtility::getLllOrHelpMessage(
                    'content.element.' . $e,
                    $loader->getExtensionKey(),
                    'tt_content'
                ),
                $typeKey,
                $config['iconExt'],
            ], 'CType', $loader->getExtensionKey());

            if (!isset($GLOBALS['TCA']['tt_content']['types'][$typeKey]['showitem']) || empty($GLOBALS['TCA']['tt_content']['types'][$typeKey]['showitem'])) {
                $baseTcaConfiguration = $this->wrapDefaultTcaConfiguration(
                    $config['fieldConfiguration'],
                    (bool)$config['noHeader']
                );

                if (ExtensionManagementUtility::isLoaded('gridelements')) {
                    $baseTcaConfiguration .= ',tx_gridelements_container,tx_gridelements_columns';
                }

                $GLOBALS['TCA']['tt_content']['types'][$typeKey]['showitem'] = $baseTcaConfiguration;
            }

            // RTE
            if (isset($config['richTextFields']) && \is_array($config['richTextFields']) && $config['richTextFields']) {
                foreach ($config['richTextFields'] as $field) {
                    $conf = [
                        'config' => [
                            'type' => 'text',
                            'enableRichtext' => '1',
                            'richtextConfiguration' => 'default',
                        ],
                    ];
                    $GLOBALS['TCA']['tt_content']['types'][$typeKey]['columnsOverrides'][$field] = $conf;
                }
            }

            IconUtility::addTcaTypeIcon('tt_content', $typeKey, $config['icon']);

            $tabName = $config['tabInformation'] ?: $loader->getExtensionKey();
            if (!\in_array($tabName, $predefinedWizards, true) && !\in_array($tabName, $createWizardHeader, true)) {
                $createWizardHeader[] = $tabName;
            }

            /** @var IconRegistry $iconRegistry */
            $provider = BitmapIconProvider::class;
            if ('svg' === mb_substr(mb_strtolower($config['iconExt']), -3)) {
                $provider = SvgIconProvider::class;
            }
            $iconRegistry = GeneralUtility::makeInstance(IconRegistry::class);
            $iconRegistry->registerIcon($tabName . '-' . $typeKey, $provider, ['source' => $config['iconExt']]);

            ExtensionManagementUtility::addPageTSConfig('
mod.wizards.newContentElement.wizardItems.' . $tabName . '.elements.' . $typeKey . ' {
    icon = ' . $config['icon'] . '
    iconIdentifier = ' . $tabName . '-' . $typeKey . '
    title = ' . TranslateUtility::getLllOrHelpMessage('wizard.' . $e, $loader->getExtensionKey()) . '
    description = ' . TranslateUtility::getLllOrHelpMessage(
                'wizard.' . $e . '.description',
                $loader->getExtensionKey()
            ) . '
    tt_content_defValues {
        CType = ' . $typeKey . '
    }
}
mod.wizards.newContentElement.wizardItems.' . $tabName . '.show := addToList(' . $typeKey . ')');
            $cObjectConfiguration = [
                'extensionKey' => $loader->getExtensionKey(),
                'backendTemplatePath' => 'EXT:' . $loader->getExtensionKey() . '/Resources/Private/Templates/Content/' . $config['model'] . 'Backend.html',
                'modelClass' => $config['modelClass'],
            ];

            $GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['ContentObject'][$loader->getExtensionKey() . '_' . GeneralUtility::camelCaseToLowerCaseUnderscored($config['model'])] = $cObjectConfiguration;
        }

        if ([] !== $createWizardHeader) {
            foreach ($createWizardHeader as $element) {
                ExtensionManagementUtility::addPageTSConfig('
mod.wizards.newContentElement.wizardItems.' . $element . ' {
    show = *
    header = ' . TranslateUtility::getLllOrHelpMessage('wizard.' . $element . '.header', $loader->getExtensionKey()) . '
}');
            }
        }
    }

    /**
     * Run the loading process for the ext_localconf.php file.
     */
    public function loadExtensionConfiguration(Loader $loader, array $loaderInformation): void
    {
        if (empty($loaderInformation)) {
            return;
        }
        static $loadPlugin = true;
        $csc = ExtensionManagementUtility::isLoaded('css_styled_content');
        $typoScript = '';

        if ($loadPlugin) {
            $loadPlugin = false;
            ExtensionUtility::configurePlugin('autoloader', 'Content', [ContentController::class => 'index'], [ContentController::class => '']);
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
        config.tx_extbase.persistence.classes.' . $config['modelClass'] . '.mapping.tableName = tt_content
        ';
        }

        if ($csc) {
            ExtensionManagementUtility::addTypoScript($loader->getExtensionKey(), 'setup', $typoScript, 43);
        } else {
            ExtensionManagementUtility::addTypoScriptSetup($typoScript);
        }
    }

    /**
     * Check if the class is tagged with noHeader.
     *
     * @param $class
     */
    protected function isTaggedWithNoHeader($class): bool
    {
        /** @var AnnotationReader $annotationReader */
        $annotationReader = GeneralUtility::makeInstance(AnnotationReader::class);

        $classNameRef = new \ReflectionClass($class);

        return null !== $annotationReader->getClassAnnotation($classNameRef, NoHeader::class);
    }

    /**
     * Check if the templates are exist and create a dummy, if there
     * is no valid template.
     */
    protected function checkAndCreateDummyTemplates(array $loaderInformation, Loader $loader): void
    {
        if (empty($loaderInformation)) {
            return;
        }

        $siteRelPathPrivate = 'EXT:' . $loader->getExtensionKey() . '/Resources/Private/';
        $frontendLayout = GeneralUtility::getFileAbsFileName($siteRelPathPrivate . 'Layouts/Content.html');
        if (!is_file($frontendLayout)) {
            $this->writeContentTemplateToTarget('FrontendLayout', $frontendLayout);
        }
        $backendLayout = GeneralUtility::getFileAbsFileName($siteRelPathPrivate . 'Layouts/ContentBackend.html');
        if (!is_file($backendLayout)) {
            $this->writeContentTemplateToTarget('BackendLayout', $backendLayout);
        }

        foreach ($loaderInformation as $configuration) {
            $templatePath = $siteRelPathPrivate . 'Templates/Content/' . $configuration['model'] . '.html';
            $absoluteTemplatePath = GeneralUtility::getFileAbsFileName($templatePath);

            $beTemplatePath = $siteRelPathPrivate . 'Templates/Content/' . $configuration['model'] . 'Backend.html';
            $absoluteBeTemplatePath = GeneralUtility::getFileAbsFileName($beTemplatePath);

            if (!is_file($absoluteTemplatePath) && !is_file($absoluteBeTemplatePath)) {
                $this->writeContentTemplateToTarget('Frontend', $absoluteTemplatePath);
                $this->writeContentTemplateToTarget('Backend', $absoluteBeTemplatePath);
            }
        }
    }

    /**
     * Write the given content object template to the target path.
     */
    protected function writeContentTemplateToTarget(string $name, string $absoluteTargetPath): void
    {
        $template = GeneralUtility::getUrl(ExtensionManagementUtility::extPath(
            'autoloader',
            'Resources/Private/Templates/ContentObjects/' . $name . '.html'
        ));
        FileUtility::writeFileAndCreateFolder($absoluteTargetPath, $template);
    }

    /**
     * Same as getClassProperties, but the fields are in LowerCaseUnderscored.
     *
     * @param $className
     *
     * @return string[]
     */
    protected function getClassPropertiesInLowerCaseUnderscored(string $className): array
    {
        $nameMapperService = GeneralUtility::makeInstance(NameMapperService::class);
        $tableName = ModelUtility::getTableName($className);

        return array_map(function ($value) use ($nameMapperService, $tableName): string {
            return $nameMapperService->getDatabaseFieldName($tableName, $value);
        }, ReflectionUtility::getDeclaringProperties($className));
    }

    /**
     * Wrap the given field configuration in the CE default TCA fields.
     */
    protected function wrapDefaultTcaConfiguration(string $configuration, bool $noHeader = false): string
    {
        $languagePrefix = 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf';
        $languagePrefixCore = 'LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf';
        $configuration = '' !== trim($configuration) && '0' !== trim($configuration) ? trim($configuration) . ',' : '';

        return '--palette--;' . $languagePrefix . ':palette.general;general,
    ' . ($noHeader ? '' : '--palette--;' . $languagePrefix . ':palette.header;header,') . '
    --div--;LLL:EXT:autoloader/Resources/Private/Language/locallang.xlf:contentData,
    ' . $configuration . '
    --div--;' . $languagePrefix . ':tabs.appearance,
    --palette--;;frames,
    --palette--;;appearanceLinks,
    --div--;' . $languagePrefixCore . ':language,
    --palette--;;language,
    --div--;' . $languagePrefixCore . ':access,
    --palette--;;hidden,
    --palette--;' . $languagePrefix . ':palette.access;access,
    --div--;' . $languagePrefixCore . ':extended';
    }

    /**
     * Get the fields that are in the default configuration.
     *
     * @param string|null $configuration
     *
     * @return mixed[]
     */
    protected function getDefaultTcaFields(bool $noHeader, $configuration = null): array
    {
        if (null === $configuration) {
            $configuration = $this->wrapDefaultTcaConfiguration('', $noHeader);
        }
        $defaultFields = [];
        // Note: TCA could be missing in install tool checks, so cast the TCA to array
        $existingFields = array_keys((array)$GLOBALS['TCA']['tt_content']['columns']);
        $parts = GeneralUtility::trimExplode(',', $configuration, true);
        foreach ($parts as $fieldConfiguration) {
            $fieldConfiguration = GeneralUtility::trimExplode(';', $fieldConfiguration, true);
            if (\in_array($fieldConfiguration[0], $existingFields, true)) {
                $defaultFields[] = $fieldConfiguration[0];
            } elseif ('--palette--' === $fieldConfiguration[0] && isset($fieldConfiguration[2])) {
                $paletteConfiguration = $GLOBALS['TCA']['tt_content']['palettes'][$fieldConfiguration[2]]['showitem'];
                if (\is_string($paletteConfiguration)) {
                    $defaultFields = array_merge(
                        $defaultFields,
                        $this->getDefaultTcaFields($noHeader, $paletteConfiguration)
                    );
                }
            }
        }

        return $defaultFields;
    }
}
