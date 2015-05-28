<?php
/**
 * Map general Models
 *
 * @category   Extension
 * @package    Autoloader\Mapper
 * @author     Tim Lochmüller <tim@fruit-lab.de>
 */

namespace HDNET\Autoloader\Mapper;

use HDNET\Autoloader\MapperInterface;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Map general Models
 *
 * @author     Tim Lochmüller <tim@fruit-lab.de>
 */
class Model implements MapperInterface {

	/**
	 * Check if the current mapper can handle the given type
	 *
	 * @param string $type
	 *
	 * @return bool
	 */
	public function canHandleType($type) {
		if (!class_exists($type)) {
			return FALSE;
		}
		try {
			$dummy = new $type();
			return ($dummy instanceof AbstractEntity);
		} catch (\Exception $exception) {
			return FALSE;
		}
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
			'userFunc' => 'HDNET\\Autoloader\\UserFunctions\\Tca->modelInfoField',
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
		return 'int(11) DEFAULT \'0\' NOT NULL';
	}
}