<?php
/**
 * JsonServer
 */

namespace HDNET\Autoloader\Utility;

use HDNET\Autoloader\Service\JsonServer as JsonServerService;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Utility\EidUtility;
use Zend\Json\Server\Request;

/**
 * JsonServer
 */
class JsonServer
{
    /**
     * @param $namespace
     * @param $method
     * @return array
     */
    public static function getNamespaceAndMethod($namespace, $method)
    {
        if (strpos($method, '.') !== false) {
            $namespace = substr($method, 0, strrpos($method, '.'));
            $method = substr($method, strrpos($method, '.') + 1);
        }

        $namespace = str_replace('.', '/', $namespace);

        return [
            $namespace,
            $method
        ];
    }

    /**
     * @param $namespace
     * @param $singleJsonRequest
     */
    public static function handleRequest($namespace, $singleJsonRequest)
    {
        $methodRaw = $singleJsonRequest['method'];
        list($namespace, $method) = self::getNamespaceAndMethod($namespace, $methodRaw);
        $singleJsonRequest['method'] = $method;

        $server = new JsonServerService($namespace, (boolean) GeneralUtility::_GP('smd'));

        $request = new Request();
        $request->setOptions($singleJsonRequest);

        $server->handle($request);
    }

    /**
     * Boostrap of Typo3 for eID environment
     */
    public static function initEnvironment()
    {
        $GLOBALS['TSFE'] = GeneralUtility::makeInstance(
            'TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController',
            $GLOBALS['TYPO3_CONF_VARS'],
            null,
            0
        );

        EidUtility::initLanguage();
        $GLOBALS['TSFE']->connectToDB();
        $GLOBALS['TSFE']->initFEuser();
        $GLOBALS['TSFE']->initUserGroups();
        EidUtility::initTCA();
        $GLOBALS['TSFE']->checkAlternativeIdMethods();
        $GLOBALS['TSFE']->clear_preview();
        $GLOBALS['TSFE']->determineId();
        $GLOBALS['TSFE']->initTemplate();
        $GLOBALS['TSFE']->getConfigArray();
        $GLOBALS['TSFE']->cObj = GeneralUtility::makeInstance(
            'TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer'
        );
        $GLOBALS['TSFE']->settingLanguage();
        $GLOBALS['TSFE']->settingLocale();
        Bootstrap::getInstance()->loadCachedTca();
    }
}
