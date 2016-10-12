<?php

namespace HDNET\Autoloader\Utility;


class EID
{
    /**
     * Boostrap of Typo3 for eID scripts
     */
    public static function init() {
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