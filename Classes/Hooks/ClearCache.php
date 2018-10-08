<?php

/**
 * Clear Cache hook for the Backend.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Hooks;

use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Toolbar\ClearCacheActionsHookInterface;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Core\ClassLoadingInformation;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

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
     * clear Cache ajax handler.
     *
     * @param array $ajaxParams
     * @param $ajaxObj
     */
    public function clear($ajaxParams, $ajaxObj)
    {
        if (!$this->isAdmin() || (!$this->isAlwaysActivated() && $this->isProduction())) {
            return;
        }

        /** @var \TYPO3\CMS\Core\Cache\CacheManager $cacheManager */
        $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
        $cacheManager->getCache('autoloader')
            ->flush();

        $composerClassLoading = true;
        if (VersionNumberUtility::convertVersionNumberToInteger(VersionNumberUtility::getNumericTypo3Version()) >= 9000000) {
            // TYPO3 >= 9.0.0
            $composerClassLoading = Environment::isComposerMode();
        } else {
            // TYPO3 < 9.0.0
            $composerClassLoading = Bootstrap::usesComposerClassLoading();
        }

        // Dump new class loading information
        if (!$composerClassLoading) {
            ClassLoadingInformation::dumpClassLoadingInformation();
        }
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
     * Return if the clear cache element is als visible in production.
     *
     * @return bool
     */
    protected function isAlwaysActivated(): bool
    {
        $configuration = \unserialize((string) $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['autoloader']);

        return isset($configuration['enableAutoloaderClearCacheInProduction']) ? (bool) $configuration['enableAutoloaderClearCacheInProduction'] : false;
    }

    /**
     * Return TRUE if the current instance is in production mode.
     *
     * @return bool
     */
    protected function isProduction(): bool
    {
        return GeneralUtility::getApplicationContext()
            ->isProduction();
    }

    /**
     * Check if the user is a admin.
     *
     * @return bool
     */
    protected function isAdmin(): bool
    {
        return \is_object($this->getBackendUserAuthentication()) && $this->getBackendUserAuthentication()
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
