<?php
/**
 * Edit link for the backend
 *
 * @author  Tim Lochmüller
 */
namespace HDNET\Autoloader\ViewHelpers\Be;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Edit link for the backend
 */
class EditLinkViewHelper extends AbstractViewHelper
{

    /**
     * Specifies whether the escaping interceptors should be disabled or enabled for the result of renderChildren() calls within this ViewHelper
     * @see isChildrenEscapingEnabled()
     *
     * Note: If this is NULL the value of $this->escapingInterceptorEnabled is considered for backwards compatibility
     *
     * @var bool
     * @api
     */
    protected $escapeChildren = false;

    /**
     * Specifies whether the escaping interceptors should be disabled or enabled for the render-result of this ViewHelper
     * @see isOutputEscapingEnabled()
     *
     * @var bool
     * @api
     */
    protected $escapeOutput = false;

    /**
     * Render a edit link for the backend preview
     *
     * @param array $data Row of the content element
     *
     * @return string
     */
    public function render(array $data)
    {
        $urlParameter = [
            'edit[tt_content][' . $data['uid'] . ']' => 'edit',
            'returnUrl' => GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL'),
        ];
        return '<a href="' . BackendUtility::getModuleUrl('record_edit', $urlParameter) . '">' . $this->renderChildren() . '</a>';
    }
}
