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

$is9orHigher = \TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(TYPO3_branch) >= \TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger('9.0');
if($is9orHigher) {
    \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('signalClass');
    \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('signalName');
    \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('noHeader');
    \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('wizardTab');
    \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('db');
    \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('recordType');
    \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('parentClass');
    \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('hook');
    \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('plugin');
    \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('noCache');
    \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('enableRichText');
    \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('parentClass');
    \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('smartExclude');
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
