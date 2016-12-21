<?php
/**
 * Content Controller
 *
 * @author Tim Lochmüller
 */

namespace HDNET\Autoloader\Controller;

use HDNET\Autoloader\Utility\ClassNamingUtility;
use HDNET\Autoloader\Utility\ExtendedUtility;
use HDNET\Autoloader\Utility\ModelUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Content Controller
 */
class ContentController extends ActionController
{

    /**
     * Render the content Element via ExtBase
     */
    public function indexAction()
    {
        $extensionKey = $this->settings['extensionKey'];
        $vendorName = $this->settings['vendorName'];
        $name = $this->settings['contentElement'];
        $data = $this->configurationManager->getContentObject()->data;

        $targetObject = ClassNamingUtility::getFqnByPath($vendorName, $extensionKey, 'Domain/Model/Content/' . $name);
        $model = ModelUtility::getModel($targetObject, $data);

        $view = $this->createStandaloneView();

        $configuration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $viewConfiguration = $configuration['view'];

        if (isset($viewConfiguration['file'])) {
            $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($viewConfiguration['file']));
        }
        if (isset($viewConfiguration['layoutRootPaths'])) {
            $view->setLayoutRootPaths($viewConfiguration['layoutRootPaths']);
        }
        if (isset($viewConfiguration['partialRootPaths'])) {
            $view->setPartialRootPaths($viewConfiguration['partialRootPaths']);
        }

        $view->assignMultiple([
            'data'     => $data,
            'object'   => $model,
            'settings' => $this->settings
        ]);
        return $view->render();
    }

    /**
     * Create a StandaloneView for the ContentObject.
     *
     * @return \TYPO3\CMS\Fluid\View\StandaloneView
     */
    protected function createStandaloneView()
    {
        $extensionKey = $this->settings['extensionKey'];
        $name = $this->settings['contentElement'];

        $templatePath = 'EXT:' . $extensionKey . '/Resources/Private/Templates/Content/' . $name . '.html';
        return ExtendedUtility::createExtensionStandaloneView($extensionKey, $templatePath);
    }
}
