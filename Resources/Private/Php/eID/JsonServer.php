<?php

use HDNET\Autoloader\Utility\JsonServer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

// Initialize Environment
JsonServer::initEnvironment();

// get JSON Request
$json = file_get_contents('php://input');

// get fallback server
$namespace = (string) GeneralUtility::_GP('server');

// check if client send a batch request and handle it,
// because Zend does not handle it itself
if (preg_match('/^\[.*\]$/', $json)) {
    $responses = [];
    foreach (json_decode($json, true) as $singleJsonRequest) {
        ob_start();
        JsonServer::handleRequest($namespace, $singleJsonRequest);
        $responses[] = json_decode(ob_get_clean());
    }

    echo json_encode($responses);
} else {
    $singleJsonRequest = json_decode($json, true);
    JsonServer::handleRequest($namespace, $singleJsonRequest);
}
