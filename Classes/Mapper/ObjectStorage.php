<?php
/**
 * Map general ObjectStorage
 *
 * @category   Extension
 * @package    Autoloader\Mapper
 * @author     Tim Lochmüller <tim@fruit-lab.de>
 */

namespace HDNET\Autoloader\Mapper;

use HDNET\Autoloader\MapperInterface;

/**
 * Map general ObjectStorage
 *
 * @author     Tim Lochmüller <tim@fruit-lab.de>
 */
class ObjectStorage implements MapperInterface {

	/**
	 * Check if the current mapper can handle the given type
	 *
	 * @param string $type
	 *
	 * @return bool
	 */
	public function canHandleType($type) {
		return stristr(trim($type, '\\'), 'typo3\\cms\\extbase\\persistence\\objectstorage') !== FALSE;
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
		$baseConfig = array(
			'type'     => 'user',
			'userFunc' => 'HDNET\\Autoloader\\UserFunctions\\Tca->objectStorageInfoField',
		);

		return array(
			'exclude' => 1,
			'label'   => $overWriteLabel ? $overWriteLabel : $fieldName,
			'config'  => $baseConfig,
		);
	}

	/**
	 * Get the database definition for the current mapper
	 *
	 * @return string
	 */
	public function getDatabaseDefinition() {
		return 'varchar(255) DEFAULT \'\' NOT NULL';
	}
}