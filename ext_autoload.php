<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$resourcesPath = ExtensionManagementUtility::extPath('autoloader', 'Resources/Private/Contrib/');

$composerFiles = $resourcesPath . 'vendor/composer/autoload_files.php';
if (file_exists($composerFiles)) {
    $files = include $composerFiles;
    foreach ($files as $file) {
        require_once($file);
    }
}

$composerClassMapPath = $resourcesPath . 'vendor/composer/autoload_classmap.php';
$classes = [];
if (is_file($composerClassMapPath)) {
    $classes = include $composerClassMapPath;
}
return $classes;
