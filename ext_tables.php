<?php
/**
 * General ext_tables file and also an example for your own extension
 *
 * @category Extension
 * @package  Autoloader
 * @author   Tim Lochmüller
 */

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\HDNET\Autoloader\Loader::extTables('HDNET', 'autoloader', [
    'Hooks',
    'Slots',
    'StaticTyposcript',
    'ExtensionId'
]);

$GLOBALS['TYPO3_CONF_VARS']['SYS']['lang']['writer'] = [
    'xlf' => 'HDNET\\Autoloader\\Localization\\Writer\\XliffWriter',
    'xml' => 'HDNET\\Autoloader\\Localization\\Writer\\XmlWriter',
];
