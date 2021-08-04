<?php

/**
 * Custom Backend Preview for Elements like Content Objects.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Hooks;

use HDNET\Autoloader\Annotation\Hook;
use HDNET\Autoloader\Utility\ExtendedUtility;
use HDNET\Autoloader\Utility\ModelUtility;
use TYPO3\CMS\Backend\View\PageLayoutView;
use TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ElementBackendPreview.
 *
 * @see  \TYPO3\CMS\Backend\View\PageLayoutView::tt_content_drawItem
 * @Hook("TYPO3_CONF_VARS|SC_OPTIONS|cms/layout/class.tx_cms_layout.php|tt_content_drawItem")
 */
class ElementBackendPreview implements PageLayoutViewDrawItemHookInterface
{
    /**
     * Preprocesses the preview rendering of a content element.
     *
     * @param PageLayoutView $parentObject  Calling parent object
     * @param bool           $drawItem      Whether to draw the item using the default functionalities
     * @param string         $headerContent Header content
     * @param string         $itemContent   Item content
     * @param array          $row           Record row of tt_content
     */
    public function preProcess(PageLayoutView &$parentObject, &$drawItem, &$headerContent, &$itemContent, array &$row): void
    {
        if (!$this->isAutoloaderContenobject($row)) {
            return;
        }

        if (!$this->hasBackendPreview($row)) {
            return;
        }

        $itemContent = $this->getBackendPreview($row);
        $drawItem = false;
    }

    /**
     * Render the Backend Preview Template and return the HTML.
     *
     * @param array $row
     *
     * @return string
     */
    public function getBackendPreview($row)
    {
        if (!$this->hasBackendPreview($row)) {
            return '';
        }

        $cacheIdentifier = 'tt-content-preview-'.$row['uid'].'-'.$row['tstamp'];

        /** @var FrontendInterface $cache */
        $cache = GeneralUtility::makeInstance(CacheManager::class)
            ->getCache('pagesection')
        ;
        if ($cache->has($cacheIdentifier)) {
            return $cache->get($cacheIdentifier);
        }

        $ctype = $row['CType'];
        /** @var array $config */
        $config = $GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['ContentObject'][$ctype];

        $model = ModelUtility::getModel($config['modelClass'], $row);

        $view = ExtendedUtility::createExtensionStandaloneView($config['extensionKey'], $config['backendTemplatePath']);
        $view->assignMultiple([
            'data' => $row,
            'object' => $model,
        ]);
        $output = $view->render();
        $cache->set($cacheIdentifier, $output);

        return $output;
    }

    /**
     * Check if the ContentObject has a Backend Preview Template.
     *
     * @param array $row
     *
     * @return bool
     */
    public function hasBackendPreview($row)
    {
        if (!$this->isAutoloaderContenobject($row)) {
            return false;
        }
        $ctype = $row['CType'];
        /** @var array $config */
        $config = $GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['ContentObject'][$ctype];

        $beTemplatePath = GeneralUtility::getFileAbsFileName($config['backendTemplatePath']);

        return is_file($beTemplatePath);
    }

    /**
     * Check if the the Element is registered by the ContenObject-Autoloader.
     *
     * @return bool
     */
    public function isAutoloaderContenobject(array $row)
    {
        $ctype = $row['CType'];

        return (bool) $GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['ContentObject'][$ctype];
    }
}
