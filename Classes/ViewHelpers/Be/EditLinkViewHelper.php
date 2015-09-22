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
        if (GeneralUtility::compat_version('7.0')) {
            $url = BackendUtility::getModuleUrl('record_edit', $urlParameter);
        } else {
            $url = $GLOBALS['BACK_PATH'] . 'alt_doc.php?' . http_build_query($urlParameter);
        }
        return '<a href="' . $url . '">' . $this->renderChildren() . '</a>';
    }
}
