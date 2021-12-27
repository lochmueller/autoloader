<?php

declare(strict_types=1);

namespace HDNET\Autoloader\Loader;

use Doctrine\Common\Annotations\AnnotationReader;
use HDNET\Autoloader\Annotation\NoHeader;
use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\Service\TyposcriptConfigurationService;
use HDNET\Autoloader\SmartObjectRegister;
use HDNET\Autoloader\Utility\ClassNamingUtility;
use HDNET\Autoloader\Utility\FileUtility;
use HDNET\Autoloader\Utility\ModelUtility;
use HDNET\Autoloader\Utility\ReflectionUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

// TODO: Add class hierarchy entry for ContentObjectLoader
class HeadlessJson implements LoaderInterface
{
    /**
     * Prepare the content object loader.
     */
    public function prepareLoader(Loader $loader, int $type): array
    {
        // no preparations, because the smart objects fill the register
        return [];
    }

    /**
     * Run the loading process for the ext_tables.php file.
     */
    public function loadExtensionTables(Loader $loader, array $loaderInformation): void
    {
        $register = SmartObjectRegister::getRegister();

        foreach ($register as $modelName) {
            if (false === \strpos($modelName, '\\Content\\')) {
                continue;
            }
            $class = new \ReflectionClass($modelName);
            if (!$class->isInstantiable()) {
                continue;
            }
            $noHeader = $this->isTaggedWithNoHeader($modelName);
            $this->checkAndCreateTyposcriptTemplate(
                $loader,
                $modelName,
                $noHeader,
                $loader->getExtensionKey(),
                $loader->getVendorName()
            );
            /*$reflectionClass = new \ReflectionClass($className);
            $fieldConfiguration = $this->getClassPropertiesInLowerCaseUnderscored($className);
            $fieldConfiguration = ReflectionUtility::getDeclaringProperties($className);*/
        }
    }

    /**
     * Run the loading process for the ext_localconf.php file.
     */
    public function loadExtensionConfiguration(Loader $loader, array $loaderInformation): void
    {
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
     * Check if the templates exist and creates it, if it does not exist.
     */
    protected function checkAndCreateTyposcriptTemplate(Loader $loader, string $className, bool $noHeader, string $extensionKey): void
    {
        $shortName = (new \ReflectionClass($className))->getShortName();
        $templatePath = 'EXT:'.$loader->getExtensionKey().'/Resources/Private/TypoScript/Content/'.$shortName.'.typoscript';
        $absoluteTemplatePath = GeneralUtility::getFileAbsFileName($templatePath);
        $template = GeneralUtility::getUrl(ExtensionManagementUtility::extPath(
            'autoloader',
            'Resources/Private/TypoScript/Content/ContentObject.typoscript'
        ));

        if (!is_file($absoluteTemplatePath)) {
            $search = [
                '__cType__',
                '__contentElementWithOrWithoutHeader__',
            ];

            $replace = [
                $loader->getExtensionKey().'_'.GeneralUtility::camelCaseToLowerCaseUnderscored($shortName),
                $noHeader ? 'contentElement' : 'contentElementWithHeader',
            ];

            $tableName = ModelUtility::getTableName($className);
            $content = str_replace($search, $replace, $template);
            $typoscriptConfigurationService = TyposcriptConfigurationService::getInstance();
            $propertyDefinitions = $typoscriptConfigurationService->getTyposcriptConfiguration($className, $extensionKey, $tableName);
            $typoscriptConfigurationService->resetSerializedCache();

            foreach ($propertyDefinitions as $propertyDefinition) {
                $content .= $propertyDefinition;
            }
            $content .= '
    }';
            FileUtility::writeFileAndCreateFolder($absoluteTemplatePath, $content);
        }
    }

    /**
     * Same as getClassProperties, but the fields are in LowerCaseUnderscored.
     *
     * @param mixed $className
     *
     * @return array
     */
    protected function getClassPropertiesInLowerCaseUnderscored($className)
    {
        return array_map(function ($value) {
            return GeneralUtility::camelCaseToLowerCaseUnderscored($value);
        }, ReflectionUtility::getDeclaringProperties($className));
    }
}
