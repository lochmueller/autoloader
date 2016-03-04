<?php

use HDNET\Autoloader\Service\SoapServer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

$server = new SoapServer((string)GeneralUtility::_GP('server'), (boolean)GeneralUtility::_GP('wsdl'));
$server->handle();
