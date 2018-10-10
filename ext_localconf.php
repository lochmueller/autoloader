<?php
/**
 * General ext_localconf file and also an example for your own extension
 *
 * @category Extension
 * @author   Tim Lochmüller
 */
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\HDNET\Autoloader\Loader::extLocalconf('HDNET', 'autoloader', [
    'Hooks',
    'Slots',
    'StaticTyposcript',
    'ExtensionId',
]);


$GLOBALS['TYPO3_CONF_VARS']['BE']['AJAX']['autoloader::clearCache'] = [
    'callbackMethod' => \HDNET\Autoloader\Hooks\ClearCache::class . '->clear',
    'csrfTokenCheck' => true
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['lang']['writer'] = [
    'xlf' => \HDNET\Autoloader\Localization\Writer\XliffWriter::class,
    'xml' => \HDNET\Autoloader\Localization\Writer\XmlWriter::class,
];


$registry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
$registry->registerIcon('extension-autoloader', \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class, [
    'source' => \TYPO3\CMS\Core\Utility\PathUtility::getAbsoluteWebPath(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName('EXT:autoloader/ext_icon.png')),
]);
