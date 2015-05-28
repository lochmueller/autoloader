<?php
/**
 * Test xclass via Globals configuration
 *
 * @category   Extension
 * @package    AutoloaderXclass
 * @author     Tim Lochmüller <tim@fruit-lab.de>
 */

namespace HDNET\AutoloaderXclass\Xclass;

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Test xclass via Globals configuration
 *
 * @author     Tim Lochmüller <tim@fruit-lab.de>
 */
class RecordList extends \TYPO3\CMS\Recordlist\RecordList {

	/**
	 * Overwrite the main and add a flash Message
	 *
	 * @return null
	 */
	public function main() {
		$this->addDummyFlashMessage();
		parent::main();
	}

	/**
	 * Add a dummy flash message to the current queue
	 */
	protected function addDummyFlashMessage() {

		$flashMessage = new FlashMessage('If you see this message, the list view is successfully extended', 'Xclass', FlashMessage::OK);

		// Add FlashMessage
		$flashMessageService = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessageService');
		/** @var $flashMessageQueue \TYPO3\CMS\Core\Messaging\FlashMessageQueue */
		$flashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();
		$flashMessageQueue->enqueue($flashMessage);
	}

} 