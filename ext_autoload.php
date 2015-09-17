<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$resourcesPath = ExtensionManagementUtility::extPath('autoloader', 'Resources/Private/Contrib/');
$composerClassMapPath = $resourcesPath . 'vendor/composer/autoload_classmap.php';
$classes = [];
if (is_file($composerClassMapPath)) {
    $classes = include $composerClassMapPath;
}
return $classes;