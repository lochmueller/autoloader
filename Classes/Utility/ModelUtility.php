<?php
/**
 * Utility to interact with the Model
 *
 * @author Tim Lochmüller
 */

namespace HDNET\Autoloader\Utility;

use HDNET\Autoloader\Persistence\ExcludeIdentityMapDataMapper;
use HDNET\Autoloader\Service\SmartObjectInformationService;
use HDNET\Autoloader\SmartObjectRegister;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Session;

/**
 * Utility to interact with the Model
 */
class ModelUtility
{

    /**
     * Get the table name by either reflection or model name
     *
     * @param $modelClassName
     *
     * @return string
     */
    public static function getTableName($modelClassName)
    {
        $reflectionName = self::getTableNameByModelReflectionAnnotation($modelClassName);
        return $reflectionName !== '' ? $reflectionName : self::getTableNameByModelName($modelClassName);
    }

    /**
     * Get the table name by reflection
     *
     * @param string $modelClassName
     *
     * @return string
     */
    public static function getTableNameByModelReflectionAnnotation($modelClassName)
    {
        return (string)ReflectionUtility::getFirstTagValue($modelClassName, 'db');
    }

    /**
     * Resolve the table name for the given class name
     * Original method from extbase core to create the table name
     *
     * @param string $className
     *
     * @return string The table name
     * @see DataMapFactory->resolveTableName
     */
    public static function getTableNameByModelName($className)
    {
        $className = ltrim($className, '\\');
        if (strpos($className, '\\') !== false) {
            $classNameParts = explode('\\', $className, 6);
            // Skip vendor and product name for core classes
            if (strpos($className, 'TYPO3\\CMS\\') === 0) {
                $classPartsToSkip = 2;
            } else {
                $classPartsToSkip = 1;
            }
            $tableName = 'tx_' . strtolower(implode('_', array_slice($classNameParts, $classPartsToSkip)));
        } else {
            $tableName = strtolower($className);
        }
        return $tableName;
    }

    /**
     * get the smart exclude values e.g. language, workspace,
     * enableFields from the given model
     *
     * @param string $name
     *
     * @return array
     */
    public static function getSmartExcludesByModelName($name)
    {
        return GeneralUtility::trimExplode(',', (string)ReflectionUtility::getFirstTagValue($name, 'smartExclude'), true);
    }

    /**
     * Get the base TCA for the given Model
     *
     * @param string $modelClassName
     *
     * @return array
     */
    public static function getTcaInformation($modelClassName)
    {
        $informationService = SmartObjectInformationService::getInstance();
        return $informationService->getTcaInformation($modelClassName);
    }

    /**
     * Get the default TCA incl. smart object fields.
     * Add missing fields to the existing TCA structure.
     *
     * @param string $extensionKey
     * @param string $tableName
     *
     * @return array
     */
    public static function getTcaOverrideInformation($extensionKey, $tableName)
    {
        $return = isset($GLOBALS['TCA'][$tableName]) ? $GLOBALS['TCA'][$tableName] : [];
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

        return $return;
    }

    /**
     * Get the target model.
     *
     * @param string $modelName
     * @param array $data
     * @param bool $backendSelection
     *
     * @return \TYPO3\CMS\Extbase\DomainObject\AbstractEntity|object
     */
    public static function getModel($modelName, $data, $backendSelection = false)
    {
        $query = ExtendedUtility::getQuery($modelName);
        $settings = $query->getQuerySettings();
        if (!$backendSelection) {
            $settings->setIgnoreEnableFields($backendSelection);
        }
        $settings->setRespectStoragePage(false);
        $settings->setRespectSysLanguage(false);

        $query->matching($query->equals('uid', $data['uid']));

        if ($backendSelection) {
            $_GET['L'] = (int)$data['sys_language_uid'];
            GeneralUtility::makeInstance(Session::class)->destroy();

            if ((isset($data['l18n_parent']) && $data['l18n_parent'] > 0) && $data['sys_language_uid']) {
                $settings->setLanguageOverlayMode(false);
                $settings->setLanguageMode(false);
                $settings->setRespectSysLanguage(true);
                $settings->setLanguageUid($data['sys_language_uid']);
            }

            $rows = $query->execute(true);
            $objectManager = new ObjectManager();
            /** @var ExcludeIdentityMapDataMapper $dataMapper */
            $dataMapper = $objectManager->get(ExcludeIdentityMapDataMapper::class);
            $objects = $dataMapper->map($modelName, $rows);
            $selection = current($objects);
            $_GET['L'] = 0;
            return $selection;
        }

        return $query->execute()
            ->getFirst();
    }
}
