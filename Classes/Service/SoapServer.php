<?php
/**
 * Soap server handling.
 *
 */

namespace HDNET\Autoloader\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use WSDL\WSDLCreator;

/**
 * Soap server handling.
 */
class SoapServer
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
     * Check if the WSDL should rendered.
     *
     * @var bool
     */
    protected $renderWsdl = false;

    /**
     * Build up the object.
     *
     * @param string $server
     * @param bool   $wsdl
     *
     * @todo move to hook logic
     */
    public function __construct($server, $wsdl)
    {
        $this->serverKey = $server;
        if (isset($GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['Soap'][$server])) {
            $this->serverClass = $GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['Soap'][$server];
        }
        $this->renderWsdl = (bool) $wsdl;
    }

    /**
     * Handle the request.
     */
    public function handle()
    {
        header('Content-Type: text/xml');
        if (!class_exists($this->serverClass)) {
            $server = new \SoapServer(null, [
                'uri' => $this->getServiceUri(),
            ]);
            $server->fault(
                2342358923745,
                'No valid server class name for the given server key: "' . $this->serverKey . '"'
            );

            return;
        }
        if ($this->renderWsdl) {
            $this->renderWsdl();
        } else {
            $this->handleRequest();
        }
    }

    /**
     * Handle the service request.
     */
    protected function handleRequest()
    {
        $server = new \SoapServer(null, [
            'uri' => $this->getServiceUri(),
        ]);
        /** @var object $object */
        $object = GeneralUtility::makeInstance($this->serverClass);
        $server->setObject($object);
        try {
            $server->handle();
        } catch (\Exception $ex) {
            $server->fault($ex->getCode(), $ex->getMessage());
        }
    }

    /**
     * Handle the WSDL request.
     */
    protected function renderWsdl()
    {
        if (!class_exists(WSDLCreator::class)) {
            throw new \Exception('If you want to use the SOAP server, please add \'"piotrooo/wsdl-creator": "1.4.2"\' to your root composer.json file.');
        }

        $uriParts = parse_url($this->getServiceUri());
        if (!is_array($uriParts)) {
            return;
        }
        unset($uriParts['query']);
        $wsdl = new WSDLCreator($this->serverClass, $this->getServiceUri());
        $wsdl->setNamespace(HttpUtility::buildUrl($uriParts));
        $wsdl->renderWSDL();
    }

    /**
     * Get the Service URI.
     *
     * @return string
     */
    protected function getServiceUri()
    {
        $uri = GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL');
        $parts = (array) parse_url($uri);
        $parts['query'] = 'eID=SoapServer&amp;server=' . $this->serverKey;

        return HttpUtility::buildUrl($parts);
    }
}
