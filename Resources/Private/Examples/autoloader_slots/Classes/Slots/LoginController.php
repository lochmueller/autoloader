<?php
/**
 * Slot for the Login controller
 *
 * @category Extension
 * @package  AutoloaderSlots\Slots
 * @author   Tim Lochmüller
 */

namespace HDNET\AutoloaderSlots\Slots;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Slot for the Login controller
 *
 * @author Tim Lochmüller
 */
class LoginController {

	/**
	 * Render the Login Form
	 *
	 * @param \TYPO3\CMS\Backend\Controller\LoginController $loginController
	 * @param array                                         $markers
	 *
	 * @return array
	 *
	 * @signalClass \TYPO3\CMS\Backend\Controller\LoginController
	 * @signalName renderLoginForm
	 */
	public function renderLoginForm(\TYPO3\CMS\Backend\Controller\LoginController $loginController, array $markers) {
		$markers['NEWS'] = $this->autoloaderTest() . $markers['NEWS'];

		return array(
			$loginController,
			$markers
		);
	}

	/**
	 * Build up the HDNET contact Info Box
	 *
	 * @return string
	 */
	protected function autoloaderTest() {
		$icon = ExtensionManagementUtility::extRelPath('autoloader') . 'ext_icon.png';
		$box = '<div id="t3-login-news-outer" class="t3-login-box">
			<div class="t3-headline">
				<h2 style="background: url(' . $icon . ') no-repeat scroll 10px center transparent;">Contact</h2>
			</div>
			<div class="t3-login-box-body">
				<dl id="t3-login-news" style="margin-top: 0;">
					<!-- ###NEWS_ITEM### begin -->
					<div class="t3-login-news-item first-item">
						<dl>
						<dt>
							<span class="t3-news-date"></span>
							<span class="t3-news-title">Autoloader</span>
						</dt>
						<dd>
							This is the Autoloader Test
						</dd>
						</dl>
					</div>
					<!-- ###NEWS_ITEM### end -->
				</dl>
			</div>
		</div>
		<div class="t3-login-box-border-bottom"></div>';

		return $box;
	}

}
