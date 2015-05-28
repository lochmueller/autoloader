<?php
/**
 * This Example test the aspects before & after.
 * This example show, that not all 'AFTER' aspects works as expected.
 *
 * @category Extension
 * @package  AutoloaderAspect\Aspect
 * @author   Carsten Biebricher
 */

namespace HDNET\AutoloaderAspect\Aspect;

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This Example test the aspects before & after.
 *
 * @author Carsten Biebricher
 */
class RecordList {

	/**
	 * Called BEFORE the main-method and add a FlashMessage to the page.
	 *
	 * @param object $object class of the joinPoint
	 * @param array  $params arguments of the joinPoint
	 *
	 * @aspectClass \TYPO3\CMS\Recordlist\RecordList
	 * @aspectJoinPoint main
	 * @aspectAdvice    before
	 */
	public function mainBefore($object, $params) {
		$flashMessage = new FlashMessage(
			'If you see this message, the list view is successfully extended with aspectAdvice::before',
			'AutoloaderAspect',
			FlashMessage::OK
		);

		// Add FlashMessage
		$flashMessageService = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessageService');
		/** @var $flashMessageQueue \TYPO3\CMS\Core\Messaging\FlashMessageQueue */
		$flashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();
		$flashMessageQueue->enqueue($flashMessage);
	}

	/**
	 * Called BEFORE the main-method and add a FlashMessage to the page.
	 *
	 * @param object $object class of the joinPoint
	 * @param array  $params arguments of the joinPoint
	 *
	 * @aspectClass \TYPO3\CMS\Recordlist\RecordList
	 * @aspectJoinPoint main
	 * @aspectAdvice    after
	 */
	public function mainAfter($object, $params) {
		$flashMessage = new FlashMessage(
			'If you see this message, the list view is successfully extended with aspectAdvice::after',
			'Aspect',
			FlashMessage::OK
		);

		// Add FlashMessage
		$flashMessageService = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessageService');
		/** @var $flashMessageQueue \TYPO3\CMS\Core\Messaging\FlashMessageQueue */
		$flashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();
		$flashMessageQueue->enqueue($flashMessage);
	}

}