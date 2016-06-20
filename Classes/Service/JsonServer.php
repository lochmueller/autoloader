<?php

/**
 * Json server handling
 *
 * @author  Tim Lochmüller
 * @author  Tito Duarte <duartito@gmail.com>
 */

namespace HDNET\Autoloader\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use Zend\Json\Server\Server;
use Zend\Json\Server\Smd;

/**
 * Json server handling
 */
class JsonServer
{

    /**
     * Server key
     *
     * @var string
     */
    protected $serverKey = '';

    /**
     * Server class
     *
     * @var string
     */
    protected $serverClass = '';

    /**
     * Check if the WSDL should rendered
     *
     * @var bool
     */
    protected $renderSmd = false;

    /**
     * Build up the object
     *
     * @param string $server
     * @param boolean $smd
     * @todo move to hook logic
     */
    public function __construct($server, $smd)
    {
        $this->serverKey = $server;
        if (isset($GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['Json'][$server])) {
            $this->serverClass = $GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['Json'][$server];
        }
        $this->renderSmd = (bool)$smd;
    }

    /**
     * Handle the request
     */
    public function handle()
    {
        header('Content-Type: application/json');
        if (!class_exists($this->serverClass)) {
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
            $this->handleRequest();
        }
    }

    /**
     * Handle the service request
     */
    protected function handleRequest()
    {
        $server = new Server();

        $server->setClass($this->serverClass);
        try {
            $server->handle();
        } catch (\Exception $ex) {
            $server->fault($ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * Handle the SMD request
     */
    protected function renderSmd()
    {
        $server = new Server();
        $server->setClass($this->serverClass);

        $smd =  $server->getServiceMap();
        $smd->setTarget($this->getServiceUri());
        $smd->setEnvelope(Smd::ENV_JSONRPC_2);
        
        echo $smd;
    }

    /**
     * Get the Service URI
     *
     * @return string
     */
    protected function getServiceUri()
    {
        $uri = GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL');
        $parts = parse_url($uri);
        $parts['query'] = 'eID=JsonServer&amp;server=' . $this->serverKey;
        return HttpUtility::buildUrl($parts);
    }
}
