<?php
/**
 * General ext_tables file and also an example for your own extension
 *
 * @category Extension
 * @author   Tim Lochmüller
 */
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}
// define TAB for XliffWriter template
defined('TAB') ?: define('TAB', chr(9));

\HDNET\Autoloader\Loader::extTables('HDNET', 'autoloader', [
    'Hooks',
    'StaticTyposcript',
]);

$GLOBALS['TYPO3_CONF_VARS']['SYS']['lang']['writer'] = [
    'xlf' => \HDNET\Autoloader\Localization\Writer\XliffWriter::class,
    'xml' => \HDNET\Autoloader\Localization\Writer\XmlWriter::class,
];
