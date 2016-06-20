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

\HDNET\Autoloader\Loader::extLocalconf('HDNET', 'autoloader', [
    'Hooks',
    'Slots',
    'StaticTyposcript',
    'ExtensionId'
]);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerAjaxHandler(
    'autoloader::clearCache',
    'HDNET\\Autoloader\\Hooks\\ClearCache->clear'
);
$GLOBALS['TYPO3_CONF_VARS']['SYS']['lang']['writer'] = [
    'xlf' => 'HDNET\\Autoloader\\Localization\\Writer\\XliffWriter',
    'xml' => 'HDNET\\Autoloader\\Localization\\Writer\\XmlWriter',
];
