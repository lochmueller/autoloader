<?php

use HDNET\Autoloader\Service\JsonServerService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

$server = new JsonServerService((string)GeneralUtility::_GP('server'), (boolean)GeneralUtility::_GP('smd'));
$server->handle();
