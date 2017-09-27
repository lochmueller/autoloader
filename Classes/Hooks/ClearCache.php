<?php
/**
 * Clear Cache hook for the Backend.
 */
namespace HDNET\Autoloader\Hooks;

use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Toolbar\ClearCacheActionsHookInterface;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Core\ClassLoadingInformation;
use TYPO3\CMS\Core\Http\AjaxRequestHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Clear Cache hook for the Backend.
 *
 * @hook TYPO3_CONF_VARS|SC_OPTIONS|additionalBackendItems|cacheActions
 */
class ClearCache implements ClearCacheActionsHookInterface
{
    /**
     * Modifies CacheMenuItems array.
     *
     * @param array $cacheActions
     * @param array $optionValues
     */
    public function manipulateCacheActions(&$cacheActions, &$optionValues)
    {
        if (!$this->isAdmin() || (!$this->isAlwaysActivated() && $this->isProduction())) {
            return;
        }

        $action = [
            'id' => 'autoloader',
            'title' => 'LLL:EXT:autoloader/Resources/Private/Language/locallang.xlf:cache.title',
            'description' => 'LLL:EXT:autoloader/Resources/Private/Language/locallang.xlf:cache.description',
            'href' => $this->getAjaxUri(),
            'iconIdentifier' => 'extension-autoloader',
        ];

        $cacheActions[] = $action;
    }

    /**
     * Get Ajax URI.
     *
     * @return string
     */
    protected function getAjaxUri(): string
    {
        /** @var UriBuilder $uriBuilder */
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        try {
            $routeIdentifier = 'ajax_autoloader::clearCache';
            $uri = $uriBuilder->buildUriFromRoute($routeIdentifier);
        } catch (RouteNotFoundException $e) {
            return '';
        }

        return (string) $uri;
    }

    /**
     * clear Cache ajax handler.
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
        $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
        $cacheManager->getCache('autoloader')
            ->flush();

        // Dump new class loading information
        if (!Bootstrap::usesComposerClassLoading()) {
            ClassLoadingInformation::dumpClassLoadingInformation();
        }
    }

    /**
     * Return if the clear cache element is als visible in production.
     *
     * @return bool
     */
    protected function isAlwaysActivated()
    {
        $configuration = unserialize((string) $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['autoloader']);

        return isset($configuration['enableAutoloaderClearCacheInProduction']) ? (bool) $configuration['enableAutoloaderClearCacheInProduction'] : false;
    }

    /**
     * Return TRUE if the current instance is in production mode.
     *
     * @return bool
     */
    protected function isProduction()
    {
        return GeneralUtility::getApplicationContext()
            ->isProduction();
    }

    /**
     * Check if the user is a admin.
     *
     * @return bool
     */
    protected function isAdmin()
    {
        return is_object($this->getBackendUserAuthentication()) && $this->getBackendUserAuthentication()
                ->isAdmin();
    }

    /**
     * Return the Backend user authentication.
     *
     * @return BackendUserAuthentication
     */
    protected function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }
}
