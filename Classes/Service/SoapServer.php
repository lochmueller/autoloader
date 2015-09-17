<?php
/**
 * Soap server handling
 *
 * @author  Tim Lochmüller
 */

namespace HDNET\Autoloader\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use WSDL\WSDLCreator;

/**
 * Soap server handling
 */
class SoapServer
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
     * @var bool
     */
    protected $renderWsdl = false;

    /**
     * @param string  $server
     * @param boolean $wsdl
     */
    public function __construct($server, $wsdl)
    {
        $this->serverKey = $server;
        if (isset($GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['Soap'][$server])) {
            $this->serverClass = $GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['Soap'][$server];
        }
        $this->renderWsdl = (bool)$wsdl;
    }

    /**
     *
     */
    public function handle()
    {
        header('Content-Type: text/xml');
        if (!class_exists($this->serverClass)) {
            $server = new \SoapServer(null, [
                'uri' => $this->getServiceUri()
            ]);
            $server->fault(2342358923745, 'No valid server class name for the given server key: "' . $this->serverClass . '"');
            return;
        }
        if ($this->renderWsdl) {
            $this->renderWsdl();
        } else {
            $this->handleRequest();
        }
    }

    /**
     *
     */
    protected function handleRequest()
    {
        $server = new \SoapServer(null, [
            'uri' => $this->getServiceUri()
        ]);
        $server->setClass($this->serverClass);
        try {
            $server->handle();
        } catch (\Exception $ex) {
            $server->fault($ex->getCode(), $ex->getMessage());
        }
    }

    /**
     *
     */
    protected function renderWsdl()
    {
        $uriParts = parse_url($this->getServiceUri());
        unset($uriParts['query']);
        $wsdl = new WSDLCreator($this->serverClass, $this->getServiceUri());
        $wsdl->setNamespace(HttpUtility::buildUrl($uriParts));
        $wsdl->renderWSDL();
    }

    /**
     * @return string
     */
    protected function getServiceUri()
    {
        $uri = GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL');
        $parts = parse_url($uri);
        $parts['query'] = 'eID=SoapServer&server=' . $this->serverKey;
        return HttpUtility::buildUrl($parts);
    }

}
