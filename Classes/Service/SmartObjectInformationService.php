<?php
/**
 * SmartObjectInformationService.php
 *
 * @author Tim Spiekerkoetter
 */

namespace HDNET\Autoloader\Service;

use HDNET\Autoloader\DataSet;
use HDNET\Autoloader\Mapper;
use HDNET\Autoloader\Utility\ArrayUtility;
use HDNET\Autoloader\Utility\ClassNamingUtility;
use HDNET\Autoloader\Utility\ExtendedUtility;
use HDNET\Autoloader\Utility\IconUtility;
use HDNET\Autoloader\Utility\ModelUtility;
use HDNET\Autoloader\Utility\ReflectionUtility;
use HDNET\Autoloader\Utility\TranslateUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * SmartObjectInformationService
 */
class SmartObjectInformationService extends AbstractService
{

    /**
     * Get a instance of this object
     *
     * @return \HDNET\Autoloader\Service\SmartObjectInformationService
     */
    public static function getInstance()
    {
        return GeneralUtility::makeInstance('HDNET\\Autoloader\\Service\\SmartObjectInformationService');
    }

    /**
     * Get database information
     *
     * @param $modelClassName
     *
     * @return string
     */
    public function getDatabaseInformation($modelClassName)
    {
        $tableName = ModelUtility::getTableName($modelClassName);
        $custom = $this->getCustomDatabaseInformation($modelClassName);

        // disable complete table generation
        // for extending existing tables
        if (ModelUtility::getTableNameByModelReflectionAnnotation($modelClassName) !== '') {
            return $this->generateSqlQuery($tableName, $custom);
        }
        return $this->generateCompleteSqlQuery($modelClassName, $tableName, $custom);
    }

    /**
     * Get the custom Model field TCA structure
     *
     * @param       $modelClassName
     * @param array $searchFields
     *
     * @return array
     */
    public function getCustomModelFieldTca($modelClassName, &$searchFields = [])
    {
        $modelInformation = ClassNamingUtility::explodeObjectModelName($modelClassName);
        $extensionName = GeneralUtility::camelCaseToLowerCaseUnderscored($modelInformation['extensionName']);
        $tableName = ModelUtility::getTableName($modelClassName);
        $customFieldInfo = $this->getCustomModelFields($modelClassName);
        $searchFields = [];
        $customFields = [];
        foreach ($customFieldInfo as $info) {
            $key = $tableName . '.' . $info['name'];
            try {
                TranslateUtility::assureLabel($key, $extensionName, $info['name']);
                $label = TranslateUtility::getLllOrHelpMessage($key, $extensionName);
            } catch (\Exception $ex) {
                $label = $info['name'];
            }

            /** @var Mapper $mapper */
            $mapper = ExtendedUtility::create('HDNET\\Autoloader\\Mapper');
            $field = $mapper->getTcaConfiguration(trim($info['var'], '\\'), $info['name'], $label);

            // RTE
            if ($info['rte']) {
                $field['config']['type'] = 'text';
                $field['defaultExtras'] = 'richtext:rte_transform[flag=rte_enabled|mode=ts_css]';
            }

            $searchFields[] = $info['name'];
            $customFields[$info['name']] = $field;
        }

        return $customFields;
    }

    /**
     * Pre build TCA information for the given model
     *
     * @param string $modelClassName
     *
     * @return array
     */
    public function getTcaInformation($modelClassName)
    {
        $modelInformation = ClassNamingUtility::explodeObjectModelName($modelClassName);
        $extensionName = GeneralUtility::camelCaseToLowerCaseUnderscored($modelInformation['extensionName']);
        $reflectionTableName = ModelUtility::getTableNameByModelReflectionAnnotation($modelClassName);
        $tableName = ModelUtility::getTableNameByModelName($modelClassName);

        $searchFields = [];
        $customFields = $this->getCustomModelFieldTca($modelClassName, $searchFields);

        if ($reflectionTableName !== '') {
            $customConfiguration = [
                'columns' => $customFields,
            ];
            $base = is_array($GLOBALS['TCA'][$reflectionTableName]) ? $GLOBALS['TCA'][$reflectionTableName] : [];
            return ArrayUtility::mergeRecursiveDistinct($base, $customConfiguration);
        }

        $excludes = ModelUtility::getSmartExcludesByModelName($modelClassName);

        $dataSet = $this->getDataSet();
        $dataImplementations = $dataSet->getAllAndExcludeList($excludes);
        $baseTca = $dataSet->getTcaInformation($dataImplementations, $tableName);

        // title
        $fields = array_keys($customFields);
        $labelField = 'title';
        if (!in_array($labelField, $fields)) {
            $labelField = $fields[0];
        }
        try {
            TranslateUtility::assureLabel($tableName, $extensionName);
        } catch (\Exception $ex) {
            // Do not handle the error of the assureLabel method
        }
        if (!is_array($baseTca['columns'])) {
            $baseTca['columns'] = [];
        }
        $baseTca['columns'] = ArrayUtility::mergeRecursiveDistinct($baseTca['columns'], $customFields);

        // items
        $showitem = $fields;
        if (!in_array('language', $excludes)) {
            $showitem[] = '--palette--;LLL:EXT:lang/locallang_general.xlf:LGL.language;language';
        }

        if (!in_array('workspaces', $excludes)) {
            $baseTca['ctrl']['shadowColumnsForNewPlaceholders'] .= ',' . $labelField;
        }

        if (GeneralUtility::compat_version('7.0')) {
            $languagePrefix = 'LLL:EXT:frontend/Resources/Private/Language/';
        } else {
            $languagePrefix = 'LLL:EXT:cms/';
        }

        if (!in_array('enableFields', $excludes)) {
            $showitem[] = '--div--;' . $languagePrefix . 'locallang_ttc.xlf:tabs.access';
            $showitem[] = '--palette--;' . $languagePrefix . 'locallang_tca.xlf:pages.palettes.access;access';
        }
        $showitem[] = '--div--;' . $languagePrefix . 'locallang_ttc.xlf:tabs.extended';

        $overrideTca = [
            'ctrl' => [
                'title' => TranslateUtility::getLllOrHelpMessage($tableName, $extensionName),
                'label' => $labelField,
                'tstamp' => 'tstamp',
                'crdate' => 'crdate',
                'cruser_id' => 'cruser_id',
                'dividers2tabs' => true,
                'sortby' => 'sorting',
                'delete' => 'deleted',
                'searchFields' => implode(',', $searchFields),
                'iconfile' => IconUtility::getByModelName($modelClassName, GeneralUtility::compat_version('7.0'))
            ],
            'interface' => [
                'showRecordFieldList' => implode(',', array_keys($baseTca['columns'])),
            ],
            'types' => [
                '1' => ['showitem' => implode(',', $showitem)],
            ],
            'palettes' => [
                'access' => ['showitem' => 'starttime, endtime, --linebreak--, hidden, editlock, --linebreak--, fe_group'],
            ],
        ];
        return ArrayUtility::mergeRecursiveDistinct($baseTca, $overrideTca);
    }

    /**
     * Get custom database information for the given model
     *
     * @param string $modelClassName
     *
     * @return string
     */
    protected function getCustomDatabaseInformation($modelClassName)
    {
        $fieldInformation = $this->getCustomModelFields($modelClassName);
        $fields = [];
        foreach ($fieldInformation as $info) {
            if ($info['db'] === '') {
                $info['db'] = $this->getDatabaseMappingByVarType($info['var']);
            } else {
                try {
                    $info['db'] = $this->getDatabaseMappingByVarType($info['db']);
                } catch (\Exception $ex) {
                    // Do not handle the getDatabaseMappingByVarType by db, Fallback is the var call
                }
            }
            $fields[] = $info['name'] . ' ' . $info['db'];
        }
        return $fields;
    }

    /**
     * Get the right mapping
     *
     * @param $var
     *
     * @throws \HDNET\Autoloader\Exception
     * @return string
     */
    protected function getDatabaseMappingByVarType($var)
    {
        /** @var Mapper $mapper */
        $mapper = ExtendedUtility::create('HDNET\\Autoloader\\Mapper');
        return $mapper->getDatabaseDefinition($var);
    }

    /**
     * Get custom database information for the given model
     *
     * @param string $modelClassName
     *
     * @return array
     */
    protected function getCustomModelFields($modelClassName)
    {
        $properties = ReflectionUtility::getPropertiesTaggedWith($modelClassName, 'db');
        $fields = [];
        foreach ($properties as $property) {
            /** @var \TYPO3\CMS\Extbase\Reflection\PropertyReflection $property */
            $var = '';
            if ($property->isTaggedWith('var')) {
                $var = $property->getTagValues('var');
                $var = $var[0];
            }

            $dbInformation = $property->getTagValues('db');
            $fields[] = [
                'name' => GeneralUtility::camelCaseToLowerCaseUnderscored($property->getName()),
                'db' => trim($dbInformation[0]),
                'var' => trim($var),
                'rte' => (bool)$property->isTaggedWith('enableRichText'),
            ];
        }
        return $fields;
    }

    /**
     * Generate SQL Query
     *
     * @param string $tableName
     * @param array $fields
     *
     * @return string
     */
    protected function generateSqlQuery($tableName, array $fields)
    {
        if (empty($fields)) {
            return '';
        }
        return LF . 'CREATE TABLE ' . $tableName . ' (' . LF . implode(',' . LF, $fields) . LF . ');' . LF;
    }

    /**
     * Generate complete SQL Query
     *
     * @param string $modelClassName
     * @param string $tableName
     * @param array $custom
     *
     * @return string
     */
    protected function generateCompleteSqlQuery($modelClassName, $tableName, array $custom)
    {
        $fields = [];
        $fields[] = 'uid int(11) NOT NULL auto_increment';
        $fields[] = 'pid int(11) DEFAULT \'0\' NOT NULL';
        $fields[] = 'tstamp int(11) unsigned DEFAULT \'0\' NOT NULL';
        $fields[] = 'crdate int(11) unsigned DEFAULT \'0\' NOT NULL';
        $fields[] = 'cruser_id int(11) unsigned DEFAULT \'0\' NOT NULL';
        $fields[] = 'deleted tinyint(4) unsigned DEFAULT \'0\' NOT NULL';
        $fields[] = 'sorting int(11) DEFAULT \'0\' NOT NULL';

        foreach ($custom as $field) {
            $fields[] = $field;
        }

        $excludes = ModelUtility::getSmartExcludesByModelName($modelClassName);
        $dataSet = $this->getDataSet();
        $dataImplementations = $dataSet->getAllAndExcludeList($excludes);

        // add data set fields
        $fields = array_merge($fields, $dataSet->getDatabaseSqlInformation($dataImplementations, $tableName));

        // default keys
        $fields[] = 'PRIMARY KEY (uid)';
        $fields[] = 'KEY parent (pid)';

        // add data set keys
        $fields = array_merge($fields, $dataSet->getDatabaseSqlKeyInformation($dataImplementations, $tableName));

        return $this->generateSqlQuery($tableName, $fields);
    }

    /**
     * Get the data set object
     *
     * @return \HDNET\Autoloader\DataSet
     */
    protected function getDataSet()
    {
        return GeneralUtility::makeInstance('HDNET\\Autoloader\\DataSet');
    }
}
