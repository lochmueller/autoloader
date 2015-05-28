<?php
/**
 * ClassNamingUtility.php
 *
 * @category Extension
 * @package  Autoloader\Utility
 * @author   Tim Spiekerkoetter
 */

namespace HDNET\Autoloader\Utility;

use HDNET\Autoloader\Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ClassNamingUtility
 *
 * @author Tim Spiekerkoetter
 */
class ClassNamingUtility {

	/**
	 * Explodes a modelName like \Vendor\Ext\Domain\Model\Foo into several
	 * pieces like vendorName, extensionName, subpackageKey and controllerName
	 *
	 * @param string $modelName The model name to be exploded
	 *
	 * @return array Parts of the object model name
	 * @throws Exception
	 * @see \TYPO3\CMS\Core\Utility\ClassNamingUtility::explodeObjectControllerName
	 */
	static public function explodeObjectModelName($modelName) {
		if (strpos($modelName, '\\') !== FALSE) {
			if (substr($modelName, 0, 9) === 'TYPO3\\CMS') {
				$extensionName = '^(?P<vendorName>[^\\\\]+\\\[^\\\\]+)\\\(?P<extensionName>[^\\\\]+)';
			} else {
				$extensionName = '^(?P<vendorName>[^\\\\]+)\\\\(?P<extensionName>[^\\\\]+)';
			}
			$regEx = '/' . $extensionName . '\\\\Domain\\\\Model\\\\(?P<modelName>[a-z0-9\\\\]+)$/ix';
		} else {
			$regEx = '/^Tx_(?P<extensionName>[^_]+)_Domain_Model_(?P<modelName>[a-z0-9_]+)/ix';
		}

		preg_match($regEx, $modelName, $matches);
		if (empty($matches)) {
			throw new Exception('Could not determine extension key for: ' . $modelName, 1406577758);
		}
		return $matches;
	}

	/**
	 * Get the extension key by the given model name
	 *
	 * @param string|object $modelClassName
	 *
	 * @return string
	 */
	static public function getExtensionKeyByModel($modelClassName) {
		if (is_object($modelClassName)) {
			$modelClassName = get_class($modelClassName);
		}
		$matches = self::explodeObjectModelName($modelClassName);
		return GeneralUtility::camelCaseToLowerCaseUnderscored($matches['extensionName']);
	}
}
