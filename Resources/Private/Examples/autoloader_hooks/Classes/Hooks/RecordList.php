<?php
/**
 * RecordList
 *
 * @category Extension
 * @package  AutoloaderHooks\Hooks
 * @author   Tim Lochmüller
 */

namespace HDNET\AutoloaderHooks\Hooks;

/**
 * RecordList
 *
 * @author Tim Lochmüller
 */
class RecordList {

	/**
	 * Draw footer hook
	 *
	 * @param array  $params
	 * @param object $parentObject
	 *
	 * @return string
	 *
	 * @hook TYPO3_CONF_VARS|SC_OPTIONS|recordlist/mod1/index.php|drawFooterHook
	 */
	public function addFooterContent($params, $parentObject) {
		return '<div style="background-color: red">
					<h1 style="color:white; padding: 20px;">
					I am additional Content at the Footer by the autoloader_hooks Extension
					</h1>
				</div>';
	}

}
