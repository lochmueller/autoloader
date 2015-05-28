<?php
/**
 * Icon helper
 *
 * @category Extension
 * @package  Autoloader\Utility
 * @author   Tim Lochmüller
 */

namespace HDNET\Autoloader\Utility;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Icon helper
 *
 * @author Tim Lochmüller
 */
class IconUtility {

	/**
	 * Get the relative path of the extension icon
	 *
	 * @param $extensionKey
	 *
	 * @return string
	 */
	public static function getByExtensionKey($extensionKey) {
		$extPath = ExtensionManagementUtility::extPath($extensionKey) . 'ext_icon.';
		$fileExtension = self::getIconFileExtension($extPath);
		if ($fileExtension) {
			return ExtensionManagementUtility::extRelPath($extensionKey) . 'ext_icon.' . $fileExtension;
		}
		return self::getByExtensionKey('autoloader');
	}

	/**
	 * Get the absolute table icon for the given model name
	 *
	 * @param string $modelClassName
	 *
	 * @return string
	 */
	static public function getByModelName($modelClassName) {
		$modelInformation = ClassNamingUtility::explodeObjectModelName($modelClassName);

		$extensionKey = GeneralUtility::camelCaseToLowerCaseUnderscored($modelInformation['extensionName']);
		$modelName = str_replace('\\', '_', $modelInformation['modelName']);

		$tableIconPath = ExtensionManagementUtility::extPath($extensionKey) . 'Resources/Public/Icons/' . $modelName . '.';
		$fileExtension = self::getIconFileExtension($tableIconPath);
		if ($fileExtension) {
			return ExtensionManagementUtility::extRelPath($extensionKey) . 'Resources/Public/Icons/' . $modelName . '.' . $fileExtension;
		}
		return self::getByExtensionKey($extensionKey);
	}

	/**
	 * Get the file extension (svg,png,gif) of the absolute path. The path is
	 * without file extension but incl. the dot. e.g.:
	 * "/test/icon."
	 *
	 * @param string $absolutePathWithoutExtension
	 *
	 * @return string
	 */
	static public function getIconFileExtension($absolutePathWithoutExtension) {
		$fileExtensions = array(
			'svg',
			'png',
			'gif',
			'jpg',
		);
		foreach ($fileExtensions as $fileExtension) {
			if (is_file($absolutePathWithoutExtension . $fileExtension)) {
				return $fileExtension;
			}
		}
		return FALSE;
	}

}
