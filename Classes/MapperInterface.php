<?php
/**
 * Mapper Interface
 *
 * @author Tim Lochmüller
 */

namespace HDNET\Autoloader;

/**
 * Mapper Interface
 */
interface MapperInterface extends SingletonInterface {

	/**
	 * Check if the current mapper can handle the given type
	 *
	 * @param string $type
	 *
	 * @return bool
	 */
	public function canHandleType($type);

	/**
	 * Get the TCA configuration for the current type
	 *
	 * @param string $fieldName
	 * @param bool   $overWriteLabel
	 *
	 * @return array
	 */
	public function getTcaConfiguration($fieldName, $overWriteLabel = FALSE);

	/**
	 * Get the database definition for the current mapper
	 *
	 * @return string
	 */
	public function getDatabaseDefinition();

} 