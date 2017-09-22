<?php
/**
 * JsonServer.
 */

namespace HDNET\Autoloader\Utility;

use HDNET\Autoloader\Service\JsonServer as JsonServerService;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Utility\EidUtility;
use Zend\Json\Server\Request;

/**
 * JsonServer.
 */
class JsonServer
{
    /**
     * @param $namespace
     * @param $method
     *
     * @return array
     */
    public static function getNamespaceAndMethod($namespace, $method)
    {
        if (false !== strpos($method, '.')) {
            $namespace = substr($method, 0, strrpos($method, '.'));
            $method = substr($method, strrpos($method, '.') + 1);
        }

        $namespace = str_replace('.', '/', $namespace);

        return [
            $namespace,
            $method,
        ];
    }

    /**
     * @param $namespace
     * @param $singleJsonRequest
     *
     * @throws \Exception
     */
    public static function handleRequest($namespace, $singleJsonRequest)
    {
        if (!class_exists(Request::class)) {
            throw new \Exception('If you want to use the JSON server, please add \'"zendframework/zend-http": "2.*", "zendframework/zend-server": "2.*", "zendframework/zend-json": "2.*"\' to your root composer.json file.');
        }
        $methodRaw = $singleJsonRequest['method'];
        list($namespace, $method) = self::getNamespaceAndMethod($namespace, $methodRaw);
        $singleJsonRequest['method'] = $method;

        $server = new JsonServerService($namespace, (bool) GeneralUtility::_GP('smd'));

        $request = new Request();
        $request->setOptions($singleJsonRequest);

        $server->handle($request);
    }

    /**
     * Boostrap of Typo3 for eID environment.
     */
    public static function initEnvironment()
    {
        $GLOBALS['TSFE'] = GeneralUtility::makeInstance(
            TypoScriptFrontendController::class,
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
            ContentObjectRenderer::class
        );
        $GLOBALS['TSFE']->settingLanguage();
        $GLOBALS['TSFE']->settingLocale();
        Bootstrap::getInstance()->loadBaseTca();
    }
}
