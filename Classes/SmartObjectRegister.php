<?php
/**
 * Register for Smart Objects
 *
 * @category Extension
 * @package  Autoloader
 * @author   Tim Lochmüller
 */

namespace HDNET\Autoloader;

/**
 * Register for Smart Objects
 *
 * @author Tim Lochmüller
 */
class SmartObjectRegister {

	/**
	 * Register for smart object information
	 *
	 * @var array
	 */
	static protected $smartObjectRegistry = array();

	/**
	 * Add a model to the register
	 *
	 * @param $modelName
	 *
	 * @return void
	 */
	static public function register($modelName) {
		if (!in_array($modelName, self::$smartObjectRegistry)) {
			self::$smartObjectRegistry[] = $modelName;
		}
	}

	/**
	 * Get the register content
	 *
	 * @return array
	 */
	static public function getRegister() {
		return self::$smartObjectRegistry;
	}

}
