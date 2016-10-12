<?php

namespace HDNET\Autoloader\eID\JsonServer;

use HDNET\Autoloader\Service\JsonServer;
use HDNET\Autoloader\Utility\EID;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Zend\Json\Server\Request;

function getNamespaceAndMethod($namespace, $method)
{
    if (strpos($method, '.') !== false) {
        $namespace = substr($method, 0, strrpos($method, '.'));
        $method = substr($method, strrpos($method, '.') + 1);
    }

    $namespace = str_replace('.', '/', $namespace);

    return array(
        $namespace,
        $method
    );
}

function handleRequest($namespace, $singleJsonRequest)
{
    $methodRaw = $singleJsonRequest['method'];
    list($namespace, $method) = getNamespaceAndMethod($namespace, $methodRaw);
    $singleJsonRequest['method'] = $method;

    $server = new JsonServer($namespace, (boolean) GeneralUtility::_GP('smd'));

    $request = new Request();
    $request->setOptions($singleJsonRequest);

    $server->handle($request);
}

// Initialize Environment
EID::init();

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
        handleRequest($namespace, $singleJsonRequest);
        $responses[] = json_decode(ob_get_clean());
    }

    echo json_encode($responses);

} else {
    $singleJsonRequest = json_decode($json, true);
    handleRequest($namespace, $singleJsonRequest);
}