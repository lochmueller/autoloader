<?php
/**
 * General ext_localconf file and also an example for your own extension
 *
 * @category Extension
 * @package  AutoloaderCommandcontroller
 * @author   Tim Lochmüller
 */

if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\HDNET\Autoloader\Loader::extLocalconf('HDNET', 'autoloader_commandcontroller', array('CommandController'));