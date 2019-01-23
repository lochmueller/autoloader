<?php

/**
 * Edit link for the backend.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\ViewHelpers\Be;

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Edit link for the backend.
 */
class EditLinkViewHelper extends AbstractViewHelper
{
    /**
     * Specifies whether the escaping interceptors should be disabled or enabled for the result of renderChildren() calls within this ViewHelper.
     *
     * @see isChildrenEscapingEnabled()
     *
     * Note: If this is NULL the value of $this->escapingInterceptorEnabled is considered for backwards compatibility
     *
     * @var bool
     *
     * @api
     */
    protected $escapeChildren = false;

    /**
     * Specifies whether the escaping interceptors should be disabled or enabled for the render-result of this ViewHelper.
     *
     * @see isOutputEscapingEnabled()
     *
     * @var bool
     *
     * @api
     */
    protected $escapeOutput = false;

    /**
     * Init arguments.
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('data', 'array', 'Row of the content element', true, []);
    }

    /**
     * Render a edit link for the backend preview.
     *
     * @return string
     */
    public function render()
    {
        $urlParameter = [
            'edit[tt_content][' . $this->arguments['data']['uid'] . ']' => 'edit',
            'returnUrl' => GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL'),
        ];

        return '<a href="' . $this->getModuleUrl('record_edit', $urlParameter) . '">' . $this->renderChildren() . '</a>';
    }

    /**
     * getModuleUrl.
     *
     * @param string $moduleName
     * @param array  $urlParameters
     *
     * @return string
     */
    protected function getModuleUrl(string $moduleName, array $urlParameters)
    {
        /** @var UriBuilder $uriBuilder */
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        try {
            if (!\method_exists($uriBuilder, 'buildUriFromRoute')) {
                throw new \Exception('No method', 1238);
            }
            $uri = $uriBuilder->buildUriFromRoute($moduleName, $urlParameters);
        } catch (\Exception $ex) {
            // old TYPO3
            $uri = BackendUtility::getModuleUrl('record_edit', $urlParameters);
        }

        return (string) $uri;
    }
}
