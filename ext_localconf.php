<?php
/**
 * General ext_localconf file and also an example for your own extension
 *
 * @category Extension
 * @package  Autoloader
 * @author   Tim Lochmüller
 */

if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\HDNET\Autoloader\Loader::extLocalconf('HDNET', 'autoloader', array(
	'Hooks',
	'Slots',
	'StaticTyposcript'
));

$GLOBALS['TYPO3_CONF_VARS']['BE']['AJAX']['autoloader::clearCache'] = 'HDNET\\Autoloader\\Hooks\\ClearCache->clear';
$GLOBALS['TYPO3_CONF_VARS']['SYS']['lang']['writer'] = array(
	'php' => 'HDNET\\Autoloader\\Localization\\Writer\\PhpWriter',
	'xlf' => 'HDNET\\Autoloader\\Localization\\Writer\\XliffWriter',
	'xml' => 'HDNET\\Autoloader\\Localization\\Writer\\XmlWriter',
);