<?php

use HDNET\Autoloader\Service\JsonServer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

$server = new JsonServer((string)GeneralUtility::_GP('server'), (boolean)GeneralUtility::_GP('smd'));
$server->handle();
