<?php
/**
 * General ext_localconf file and also an example for your own extension
 *
 * @category Extension
 * @author   Tim Lochmüller
 */
// Note: Drop TYPO3_MODE if extension is TYPO3 >= v11 only
defined('TYPO3_MODE') or defined('TYPO3') or die();

\HDNET\Autoloader\Loader::extLocalconf('HDNET', 'autoloader', [
    'Hooks',
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
