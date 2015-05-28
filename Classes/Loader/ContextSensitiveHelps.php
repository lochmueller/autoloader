<?php
/**
 * ContextSensitiveHelp (CSH) based on smart objects
 *
 * @category Extension
 * @package  Autoloader\Loader
 * @author   Tim Lochmüller
 */

namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Loader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\Service\SmartObjectInformationService;
use HDNET\Autoloader\SmartObjectRegister;
use HDNET\Autoloader\Utility\ClassNamingUtility;
use HDNET\Autoloader\Utility\ModelUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ContextSensitiveHelp (CSH) based on smart objects
 *
 * @author     Tim Lochmüller
 */
class ContextSensitiveHelps implements LoaderInterface {

	/**
	 * Get all the complex data for the loader.
	 * This return value will be cached and stored in the database
	 * There is no file monitoring for this cache
	 *
	 * @param Loader $loader
	 * @param int    $type
	 *
	 * @return array
	 */
	public function prepareLoader(Loader $loader, $type) {
		$modelInformation = $this->findTableAndModelInformationForExtension($loader->getExtensionKey());
		$tables = array();
		foreach ($modelInformation as $information) {
			$tables[] = $information['table'];
			$path = 'EXT:' . $loader->getExtensionKey() . '/Resources/Private/Language/locallang_csh_' . $information['table'] . '.xml';
			$realPath = GeneralUtility::getFileAbsFileName($path);
			if ($type === LoaderInterface::EXT_TABLES) {
				$this->checkCshFile($realPath, $information['class']);
			}
		}

		return $tables;
	}

	/**
	 * Find table and model information for the given extension key
	 *
	 * @param string $extensionKey
	 *
	 * @return array
	 */
	protected function findTableAndModelInformationForExtension($extensionKey) {
		$information = array();
		$register = SmartObjectRegister::getRegister();
		foreach ($register as $class) {
			$parts = ClassNamingUtility::explodeObjectModelName($class);
			if (GeneralUtility::camelCaseToLowerCaseUnderscored($parts['extensionName']) === $extensionKey) {
				if (ModelUtility::getTableNameByModelReflectionAnnotation($class) === '') {
					$information[] = array(
						'table' => ModelUtility::getTableNameByModelName($class),
						'class' => $class
					);
				}
			}
		}

		return $information;
	}

	/**
	 * Check if the given file is already existing
	 *
	 * @param $path
	 * @param $modelClass
	 *
	 * @return void
	 */
	protected function checkCshFile($path, $modelClass) {
		if (is_file($path)) {
			return;
		}
		$information = SmartObjectInformationService::getInstance()
			->getCustomModelFieldTca($modelClass);
		$properties = array_keys($information);

		$templatePath = 'Resources/Private/Templates/ContextSensitiveHelp/LanguageDescription.xml';
		$standaloneView = GeneralUtility::makeInstance('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
		$standaloneView->setTemplatePathAndFilename(ExtensionManagementUtility::extPath('autoloader', $templatePath));
		$standaloneView->assign('properties', $properties);
		$content = $standaloneView->render();

		GeneralUtility::writeFile($path, $content);
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
		foreach ($loaderInformation as $table) {
			$path = 'EXT:' . $loader->getExtensionKey() . '/Resources/Private/Language/locallang_csh_' . $table . '.xml';
			ExtensionManagementUtility::addLLrefForTCAdescr($table, $path);
		}

		return NULL;
	}

	/**
	 * Run the loading process for the ext_localconf.php file
	 *
	 * @param \HDNET\Autoloader\Loader $loader
	 * @param array                    $loaderInformation
	 *
	 * @internal param \HDNET\Autoloader\Loader $autoLoader
	 * @return NULL
	 */
	public function loadExtensionConfiguration(Loader $loader, array $loaderInformation) {
		return NULL;
	}
}