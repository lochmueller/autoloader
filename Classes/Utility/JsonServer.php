<?php
namespace HDNET\Autoloader\Utility;

use HDNET\Autoloader\Service\JsonServer as JsonServerService;
use Zend\Json\Server\Request;

class JsonServer
{
    public static function getNamespaceAndMethod($namespace, $method)
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
    public static function initEnvironment() {
        /* @var $GLOBALS['TSFE'] \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController */
        $GLOBALS['TSFE'] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController::class, $GLOBALS['TYPO3_CONF_VARS'], null, 0);

        \TYPO3\CMS\Frontend\Utility\EidUtility::initLanguage();
        $GLOBALS['TSFE']->connectToDB();
        $GLOBALS['TSFE']->initFEuser();
        $GLOBALS['TSFE']->initUserGroups();
        \TYPO3\CMS\Frontend\Utility\EidUtility::initTCA();
        $GLOBALS['TSFE']->checkAlternativeIdMethods();
        $GLOBALS['TSFE']->clear_preview();
        $GLOBALS['TSFE']->determineId();
        $GLOBALS['TSFE']->initTemplate();
        $GLOBALS['TSFE']->getConfigArray();
        $GLOBALS['TSFE']->cObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer::class);
        $GLOBALS['TSFE']->settingLanguage();
        $GLOBALS['TSFE']->settingLocale();
        \TYPO3\CMS\Core\Core\Bootstrap::getInstance()->loadCachedTca();
    }
}