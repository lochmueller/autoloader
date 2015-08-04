<?php
/**
 * Handling of the language files
 *
 * @author  Tim Lochmüller
 */

namespace HDNET\Autoloader\Localization;

use TYPO3\CMS\Core\Localization\LanguageStore;

/**
 * Handling of the language files
 *
 * @author Tim Lochmüller
 */
class LanguageHandler extends LanguageStore {

	/**
	 * @todo implement handler for writing language files
	 *
	 * @param string $key       key in the localization file
	 * @param string $extensionName
	 * @param string $default   default value of the label
	 * @param array  $arguments arguments are being passed over to vsprintf
	 */
	public function handle($key, $extensionName, &$default, $arguments) {
		#var_dump($key);
		#var_dump($extensionName);
		#var_dump($default);
		#var_dump($arguments);
		#die('Test');
		#
		#		var_dump();
		// $this->getSupportedExtensions()

	}

}
