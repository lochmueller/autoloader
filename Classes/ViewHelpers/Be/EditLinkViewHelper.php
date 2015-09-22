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
            'returnUrl'                              => GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL'),
        ];
        $url = BackendUtility::getModuleUrl('record_edit', $urlParameter);
        if (!GeneralUtility::compat_version('7.0')) {
            $url = $this->buildCompatibilityUrl($urlParameter);
        }
        return '<a href="' . $url . '">' . $this->renderChildren() . '</a>';
    }

    /**
     * Build the URI for the TYPO3 < 7.0
     *
     * @param array $urlParameter
     *
     * @return string
     */
    protected function buildCompatibilityUrl(array $urlParameter)
    {
        return 'alt_doc.php?' . http_build_query($urlParameter);
    }
}
