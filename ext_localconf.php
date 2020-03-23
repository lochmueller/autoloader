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
]);

$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['autoloader7'] = [
    'backend' => \HDNET\Autoloader\Cache\AutoloaderFileBackend::class,
    'groups' => [
        'system',
    ],
    'options' => [
        'defaultLifetime' => 0,
    ],
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['lang']['writer'] = [
    'xlf' => \HDNET\Autoloader\Localization\Writer\XliffWriter::class,
    'xml' => \HDNET\Autoloader\Localization\Writer\XmlWriter::class,
];

$registry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
$registry->registerIcon('extension-autoloader', \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class, [
    'source' => 'EXT:autoloader/Resources/Public/Icons/Extension.svg',
]);
$registry->registerIcon('extension-autoloader-clearcache', \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class, [
    'source' => 'EXT:autoloader/Resources/Public/Icons/ClearCache.svg',
]);
