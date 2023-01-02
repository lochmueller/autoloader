<?php

/**
 * Utility to interact with the Model.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Utility;

use Doctrine\Common\Annotations\AnnotationReader;
use HDNET\Autoloader\Annotation\DatabaseField;
use HDNET\Autoloader\Annotation\DatabaseTable;
use HDNET\Autoloader\Annotation\EnableRichText;
use HDNET\Autoloader\Annotation\SmartExclude;
use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\Service\NameMapperService;
use HDNET\Autoloader\Service\SmartObjectInformationService;
use HDNET\Autoloader\SmartObjectRegister;
use HDNET\Autoloader\TcaLoaderInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Session;

/**
 * Utility to interact with the Model.
 */
class ModelUtility
{
    /**
     * Get the table name by either reflection or model name.
     *
     * @param $modelClassName
     */
    public static function getTableName($modelClassName): string
    {
        $reflectionName = self::getTableNameByModelReflectionAnnotation($modelClassName);

        return '' !== $reflectionName ? $reflectionName : self::getTableNameByModelName($modelClassName);
    }

    /**
     * Get the table name by reflection.
     */
    public static function getTableNameByModelReflectionAnnotation(string $modelClassName): string
    {
        /** @var AnnotationReader $annotationReader */
        $annotationReader = GeneralUtility::makeInstance(AnnotationReader::class);
        $classAnnotation = $annotationReader->getClassAnnotation(new \ReflectionClass($modelClassName), DatabaseTable::class);

        if (!$classAnnotation instanceof DatabaseTable) {
            return '';
        }

        return (string)$classAnnotation->tableName;
    }

    /**
     * Resolve the table name for the given class name
     * Original method from extbase core to create the table name.
     *
     * @return string The table name
     *
     * @see DataMapFactory->resolveTableName
     */
    public static function getTableNameByModelName(string $className): string
    {
        $className = ltrim($className, '\\');
        if (false !== mb_strpos($className, '\\')) {
            $classNameParts = explode('\\', $className);
            // Skip vendor and product name for core classes
            $classPartsToSkip = 0 === mb_strpos($className, 'TYPO3\\CMS\\') ? 2 : 1;
            $classNameParts = \array_slice($classNameParts, $classPartsToSkip);
            $classNameParts = explode('\\', implode('\\', $classNameParts), 4);
            $tableName = 'tx_' . str_replace('\\', '_', mb_strtolower(implode('_', $classNameParts)));
        } else {
            $tableName = mb_strtolower($className);
        }

        return $tableName;
    }

    /**
     * get the smart exclude values e.g. language, workspace,
     * enableFields from the given model.
     *
     * @return mixed[]
     */
    public static function getSmartExcludesByModelName(string $name): array
    {
        /** @var AnnotationReader $annotationReader */
        $annotationReader = GeneralUtility::makeInstance(AnnotationReader::class);

        $reflectionClass = new \ReflectionClass($name);

        $smartExcludes = $annotationReader->getClassAnnotation($reflectionClass, SmartExclude::class);

        return $smartExcludes instanceof SmartExclude ? $smartExcludes->excludes : [];
    }

    /**
     * Get the base TCA for the given Model.
     *
     * @return mixed[]
     */
    public static function getTcaInformation(string $modelClassName): array
    {
        $informationService = SmartObjectInformationService::getInstance();

        return $informationService->getTcaInformation($modelClassName);
    }

    /**
     * Get the default TCA incl. smart object fields.
     * Add missing fields to the existing TCA structure.
     *
     * @return mixed[]
     */
    public static function getTcaOverrideInformation(string $extensionKey, string $tableName): array
    {
        $return = $GLOBALS['TCA'][$tableName] ?? [];
        $classNames = SmartObjectRegister::getRegister();
        $informationService = SmartObjectInformationService::getInstance();

        foreach ($classNames as $className) {
            if (ClassNamingUtility::getExtensionKeyByModel($className) !== $extensionKey) {
                continue;
            }
            if (self::getTableNameByModelReflectionAnnotation($className) === $tableName) {
                $additionalTca = $informationService->getCustomModelFieldTca($className);
                foreach ($additionalTca as $fieldName => $configuration) {
                    if (!isset($return['columns'][$fieldName])) {
                        $return['columns'][$fieldName] = $configuration;
                    }
                }
            }
        }

        // via TcaLoaderInterface
        $implemetations = $GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['Implementations'][$extensionKey] ?? false;
        if(is_array($implemetations)) {
            $GLOBALS['TCA'][$tableName] = $return;
            $loader = GeneralUtility::makeInstance(Loader::class);
            $objects = $loader->buildAutoLoaderObjects($implemetations);
            $information = $loader->prepareAutoLoaderObjects($objects, LoaderInterface::EXT_LOCAL_CONFIGURATION);
            foreach ($objects as $object) {
                if($object instanceof TcaLoaderInterface) {
                    $object->loadTcaConfiguration($loader, $information[\get_class($object)], $extensionKey, $tableName);
                }
            }
            $return = $GLOBALS['TCA'][$tableName];
        }

        return $return;
    }

    /**
     * Get the target model.
     *
     * @param mixed[] $data
     *
     * @return object|\TYPO3\CMS\Extbase\DomainObject\AbstractEntity
     */
    public static function getModel(string $modelName, array $data): ?object
    {
        // Base query
        $query = ExtendedUtility::getQuery($modelName);
        $settings = $query->getQuerySettings();
        $settings->setRespectStoragePage(false);
        $settings->setRespectSysLanguage(false);
        $query->matching($query->equals('uid', $data['uid']));

        // Note: Change TYPO3_MODE if extension is TYPO3 >= v11 only
        // https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/11.0/Deprecation-92947-DeprecateTYPO3_MODEAndTYPO3_REQUESTTYPEConstants.html#typo3-mode-and-typo3-requesttype-usages-in-class-files
        if (TYPO3_MODE === 'BE') {
            GeneralUtility::makeInstance(Session::class)->destroy();
            $settings->setIgnoreEnableFields(true);

            if (isset($data['sys_language_uid']) && (int)$data['sys_language_uid'] > 0) {
                $_GET['L'] = (int)$data['sys_language_uid'];

                if (isset($data['l18n_parent']) && $data['l18n_parent'] > 0) {
                    $settings->setLanguageOverlayMode(false);
                    $settings->setLanguageMode(null);
                    $settings->setRespectSysLanguage(true);
                    $settings->setLanguageUid((int)$data['sys_language_uid']);
                }

                return $query->execute()->getFirst();
            }
        }

        $query->matching($query->equals('uid', $data['uid']));

        return $query->execute()
            ->getFirst()
        ;
    }

    /**
     * Get custom database information for the given model.
     *
     * @return array<int, array<string, bool|string>>
     */
    public static function getCustomModelFields(string $modelClassName): array
    {
        /** @var AnnotationReader $annotationReader */
        $annotationReader = GeneralUtility::makeInstance(AnnotationReader::class);

        $reflectionClass = new \ReflectionClass($modelClassName);
        $properties = [];
        foreach ($reflectionClass->getProperties() as $property) {
            $propertiesCheck = $annotationReader->getPropertyAnnotation($property, DatabaseField::class);
            if (null !== $propertiesCheck) {
                $properties[$property->getName()] = $propertiesCheck;
            }
        }

        $tableName = self::getTableName($modelClassName);
        $nameMapperService = GeneralUtility::makeInstance(NameMapperService::class);
        $fields = [];

        foreach ($properties as $name => $annotation) {
            $var = (string)$annotation->type;
            $fields[] = [
                'property' => $name,
                'name' => $nameMapperService->getDatabaseFieldName($tableName, $name),
                'db' => trim((string)$annotation->sql),
                'var' => trim((string)$var),
                'rte' => null !== $annotationReader->getPropertyAnnotation($reflectionClass->getProperty($name), EnableRichText::class),
            ];
        }

        return $fields;
    }
}
