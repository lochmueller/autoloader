<?php
/**
 * Map FileReference
 *
 * @category   Extension
 * @package    Autoloader\Mapper
 * @author     Tim Lochmüller <tim@fruit-lab.de>
 */

namespace HDNET\Autoloader\Mapper;

use HDNET\Autoloader\MapperInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Map FileReference
 *
 * @author     Tim Lochmüller <tim@fruit-lab.de>
 */
class FileReference implements MapperInterface {

	/**
	 * Check if the current mapper can handle the given type
	 *
	 * @param string $type
	 *
	 * @return bool
	 */
	public function canHandleType($type) {
		return in_array(strtolower(trim($type, '\\')), array(
			'typo3\\cms\\extbase\\domain\\model\\filereference',
		));
	}

	/**
	 * Get the TCA configuration for the current type
	 *
	 * @param string $fieldName
	 * @param bool   $overWriteLabel
	 *
	 * @return array
	 */
	public function getTcaConfiguration($fieldName, $overWriteLabel = FALSE) {
		return array(
			'exclude' => 1,
			'label'   => $overWriteLabel ? $overWriteLabel : $fieldName,
			'config'  => ExtensionManagementUtility::getFileFieldTCAConfig($fieldName, array('maxitems' => 1)),
		);
	}

	/**
	 * Get the database definition for the current mapper
	 *
	 * @return string
	 */
	public function getDatabaseDefinition() {
		return 'int(11) DEFAULT \'0\' NOT NULL';
	}
}