<?php
/**
 * Second controller
 *
 * @package AutoloaderPlugin
 * @author  Tim Lochmüller
 */

namespace HDNET\AutoloaderPlugin\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Second controller
 *
 * @author Tim Lochmüller
 */
class SecondController extends ActionController {

	/**
	 * @plugin Second
	 */
	public function secondAction() {

	}

	/**
	 * @plugin Second
	 * @noCache
	 */
	public function aNoCacheAction() {

	}
}