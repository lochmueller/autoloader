<?php
/**
 * Management for Smart Objects
 *
 * @category Extension
 * @package  Autoloader
 * @author   Tim Lochmüller
 */

namespace HDNET\Autoloader;

use HDNET\Autoloader\Service\SmartObjectInformationService;
use HDNET\Autoloader\Utility\ClassNamingUtility;
use HDNET\Autoloader\Utility\FileUtility;
use HDNET\Autoloader\Utility\ModelUtility;
use HDNET\Autoloader\Utility\ReflectionUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Management for Smart Objects
 *
 * @author Tim Lochmüller
 */
class SmartObjectManager implements SingletonInterface {

	/**
	 * Return the SQL String for all registered smart objects
	 *
	 * @return string
	 */
	static public function getSmartObjectRegisterSql() {
		$informationService = SmartObjectInformationService::getInstance();
		$register = SmartObjectRegister::getRegister();

		$output = array();
		foreach ($register as $modelName) {
			$output[] = $informationService->getDatabaseInformation($modelName);
		}
		return implode(LF, $output);
	}

	/**
	 * Check if the given class is a smart object
	 *
	 * Also add a work around, because the static_info_tables SPL Autoloader
	 * get into a conflict with different classes.
	 *
	 * @param string $className
	 *
	 * @return bool
	 */
	static public function isSmartObjectClass($className) {
		$riskAutoLoader = array(
			'SJBR\\StaticInfoTables\\Cache\\CachedClassLoader',
			'autoload'
		);
		$registerAutoLoader = spl_autoload_unregister($riskAutoLoader);

		if (!class_exists($className)) {
			$return = FALSE;
		} else {
			$classReflection = ReflectionUtility::createReflectionClass($className);
			$return = !(bool)(!$classReflection->isInstantiable() || !$classReflection->isTaggedWith('db'));
		}

		if ($registerAutoLoader) {
			spl_autoload_register($riskAutoLoader, TRUE, TRUE);
		}

		return $return;
	}

	/**
	 * Check and create the TCA information
	 * disable this for better performance
	 */
	static public function checkAndCreateTcaInformation() {
		$register = SmartObjectRegister::getRegister();

		$baseTemplatePath = ExtensionManagementUtility::extPath('autoloader', 'Resources/Private/Templates/TcaFiles/');
		$defaultTemplate = GeneralUtility::getUrl($baseTemplatePath . 'Default.tmpl');
		$overrideTemplate = GeneralUtility::getUrl($baseTemplatePath . 'Override.tmpl');

		$search = array(
			'__modelName__',
			'__tableName__',
			'__extensionKey__',
		);

		foreach ($register as $model) {
			$extensionKey = ClassNamingUtility::getExtensionKeyByModel($model);
			$basePath = ExtensionManagementUtility::extPath($extensionKey) . 'Configuration/TCA/';

			$tableName = ModelUtility::getTableNameByModelReflectionAnnotation($model);
			if ($tableName !== '') {
				$tcaFileName = $basePath . 'Overrides/' . $tableName . '.php';
				$template = $overrideTemplate;
			} else {
				$tableName = ModelUtility::getTableNameByModelName($model);
				$tcaFileName = $basePath . $tableName . '.php';
				$template = $defaultTemplate;
			}

			if (!is_file($tcaFileName)) {
				$replace = array(
					str_replace('\\', '\\\\', $model),
					$tableName,
					$extensionKey
				);

				$content = str_replace($search, $replace, $template);
				FileUtility::writeFileAndCreateFolder($tcaFileName, $content);
			}
		}
	}

}
