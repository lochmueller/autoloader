<?php
/**
 * Test command controller
 *
 * @category   Extension
 * @package    AutoloaderCommandcontroller
 * @author     Tim Lochmüller <tim@fruit-lab.de>
 */


namespace HDNET\AutoloaderCommandcontroller\Command;

use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

/**
 * Test command controller
 *
 * @author     Tim Lochmüller <tim@fruit-lab.de>
 */
class TestCommandController extends CommandController {

	/**
	 * Run a empty Test command
	 */
	public function testCommand() {

	}

	/**
	 * Run a test command
	 *
	 * @param string  $text
	 * @param bool $boolTest
	 */
	public function runCommand($text, $boolTest) {

	}
} 