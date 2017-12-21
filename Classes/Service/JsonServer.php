<?php

/**
 * Json server handling.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Service;

use HDNET\Autoloader\Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use Zend\Json\Server\Server;
use Zend\Json\Server\Smd;

/**
 * Json server handling.
 */
class JsonServer
{
    /**
     * Server key.
     *
     * @var string
     */
    protected $serverKey = '';

    /**
     * Server class.
     *
     * @var string
     */
    protected $serverClass = '';

    /**
     * Check if the SMD should rendered.
     *
     * @var bool
     */
    protected $renderSmd = false;

    /**
     * Build up the object.
     *
     * @param string $server
     * @param bool   $smd
     */
    public function __construct($server, $smd)
    {
        $this->serverKey = $server;
        if (isset($GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['Json'][$server])) {
            $this->serverClass = $GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['Json'][$server];
        }

        $this->renderSmd = (bool) $smd;
    }

    /**
     * Handle the request.
     *
     * @param $request
     *
     * @throws \Exception
     */
    public function handle($request)
    {
        if (!\class_exists(Server::class)) {
            throw new Exception('If you want to use the JSON server, please add \'"zendframework/zend-http": "2.*", "zendframework/zend-server": "2.*", "zendframework/zend-json": "2.*"\' to your root composer.json file.');
        }

        \header('Content-Type: application/json');
        if (!\class_exists($this->serverClass)) {
            $server = new Server();
            echo $server->fault(
                'No valid server class name for the given server key: "' . $this->serverKey . '"',
                2342358923745
            );

            return;
        }

        if ($this->renderSmd) {
            $this->renderSmd();
        } else {
            $this->handleRequest($request);
        }
    }

    /**
     * Handle the service request.
     *
     * @param $request
     */
    protected function handleRequest($request)
    {
        $server = new Server();
        $server->setRequest($request);

        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $server->setClass($objectManager->get($this->serverClass));
        try {
            $server->handle();
        } catch (\Exception $ex) {
            echo $server->fault($ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * Handle the SMD request.
     */
    protected function renderSmd()
    {
        $server = new Server();
        $server->setClass($this->serverClass);

        $smd = $server->getServiceMap();
        $smd->setTarget($this->getServiceUri());
        $smd->setEnvelope(Smd::ENV_JSONRPC_2);

        echo $smd;
    }

    /**
     * Get the Service URI.
     *
     * @return string
     */
    protected function getServiceUri()
    {
        $uri = GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL');
        $parts = \parse_url($uri);
        $parts['query'] = 'eID=JsonServer&server=' . $this->serverKey;

        return HttpUtility::buildUrl($parts);
    }
}
