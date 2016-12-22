<?php
/**
 * Clear Cache hook for the Backend
 *
 * @author Tim Lochmüller
 */

namespace HDNET\Autoloader\Hooks;

use HDNET\Autoloader\Utility\IconUtility;
use TYPO3\CMS\Backend\Toolbar\ClearCacheActionsHookInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Core\ClassLoadingInformation;
use TYPO3\CMS\Core\Http\AjaxRequestHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Clear Cache hook for the Backend
 *
 * @hook TYPO3_CONF_VARS|SC_OPTIONS|additionalBackendItems|cacheActions
 */
class ClearCache implements ClearCacheActionsHookInterface
{

    /**
     * Modifies CacheMenuItems array
     *
     * @param array $cacheActions
     * @param array $optionValues
     *
     * @return void
     */
    public function manipulateCacheActions(&$cacheActions, &$optionValues)
    {
        if (!$this->isAdmin() || (!$this->isAlwaysActivated() && $this->isProduction())) {
            return;
        }

        $cacheActions[] = [
            'id'    => 'autoloader',
            'title' => 'EXT:autoloader caches',
            'href'  => BackendUtility::getAjaxUrl('autoloader::clearCache'),
            'icon'  => '<img src="' . IconUtility::getByExtensionKey('autoloader') . '">',
        ];
    }

    /**
     * clear Cache ajax handler
     *
     * @param array              $ajaxParams
     * @param AjaxRequestHandler $ajaxObj
     */
    public function clear($ajaxParams, AjaxRequestHandler $ajaxObj)
    {
        if (!$this->isAdmin() || (!$this->isAlwaysActivated() && $this->isProduction())) {
            return;
        }

        /** @var \TYPO3\CMS\Core\Cache\CacheManager $cacheManager */
        $cacheManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheManager');
        $cacheManager->getCache('autoloader')
            ->flush();

        // Dump new class loading information
        if (GeneralUtility::compat_version('7.0') && !Bootstrap::usesComposerClassLoading()) {
            ClassLoadingInformation::dumpClassLoadingInformation();
        }
    }

    /**
     * Return if the clear cache element is als visible in production
     *
     * @return bool
     */
    protected function isAlwaysActivated()
    {
        $configuration = unserialize((string)$GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['autoloader']);
        return isset($configuration['enableAutoloaderClearCacheInProduction']) ? (bool)$configuration['enableAutoloaderClearCacheInProduction'] : false;
    }

    /**
     * Return TRUE if the current instance is in production mode
     *
     * @return bool
     */
    protected function isProduction()
    {
        return GeneralUtility::getApplicationContext()
            ->isProduction();
    }

    /**
     * Check if the user is a admin
     *
     * @return bool
     */
    protected function isAdmin()
    {
        return is_object($this->getBackendUserAuthentication()) && $this->getBackendUserAuthentication()
            ->isAdmin();
    }

    /**
     * Return the Backend user authentication
     *
     * @return BackendUserAuthentication
     */
    protected function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }
}
