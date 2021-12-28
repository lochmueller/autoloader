<?php

/**
 * Add backend layouts.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Hooks;

use HDNET\Autoloader\Annotation\Hook;
use TYPO3\CMS\Backend\View\BackendLayout\BackendLayout;
use TYPO3\CMS\Backend\View\BackendLayout\BackendLayoutCollection;
use TYPO3\CMS\Backend\View\BackendLayout\DataProviderContext;
use TYPO3\CMS\Backend\View\BackendLayout\DataProviderInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Add backend layouts.
 *
 * @see  https://github.com/georgringer/TYPO3.base/blob/master/typo3conf/ext/theme/Classes/View/BackendLayout/FileProvider.php
 * @Hook("TYPO3_CONF_VARS|SC_OPTIONS|BackendLayoutDataProvider")
 */
class BackendLayoutProvider implements DataProviderInterface
{
    /**
     * Layout information.
     *
     * @var mixed[]
     */
    protected static $backendLayoutInformation = [];

    /**
     * Add one backend layout information.
     */
    public static function addBackendLayoutInformation(array $backendLayout): void
    {
        self::$backendLayoutInformation[] = $backendLayout;
    }

    /**
     * Adds backend layouts to the given backend layout collection.
     */
    public function addBackendLayouts(DataProviderContext $dataProviderContext, BackendLayoutCollection $backendLayoutCollection): void
    {
        foreach (self::$backendLayoutInformation as $info) {
            $backendLayoutCollection->add($this->createBackendLayout($info));
        }
    }

    /**
     * Gets a backend layout by (regular) identifier.
     *
     * @param string $identifier
     * @param int    $pageId
     *
     * @return \TYPO3\CMS\Backend\View\BackendLayout\BackendLayout|void
     */
    public function getBackendLayout($identifier, $pageId)
    {
        foreach (self::$backendLayoutInformation as $info) {
            if ($this->getIdentifier($info) === $identifier) {
                return $this->createBackendLayout($info);
            }
        }
    }

    /**
     * Create a backend layout with the given information.
     *
     * @param $info
     *
     * @return mixed
     */
    protected function createBackendLayout($info): BackendLayout
    {
        $fileName = GeneralUtility::getFileAbsFileName($info['path']);
        $backendLayout = BackendLayout::create($this->getIdentifier($info), $info['label'], GeneralUtility::getUrl($fileName));
        if ($info['icon']) {
            $backendLayout->setIconPath($info['icon']);
        }

        return $backendLayout;
    }

    /**
     * Get identifier.
     *
     * @param $info
     */
    protected function getIdentifier($info): string
    {
        return $info['extension'] . '/' . $info['filename'];
    }
}
