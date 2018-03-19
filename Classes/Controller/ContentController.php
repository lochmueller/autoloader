<?php

/**
 * Content Controller.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Controller;

use HDNET\Autoloader\Utility\ClassNamingUtility;
use HDNET\Autoloader\Utility\ExtendedUtility;
use HDNET\Autoloader\Utility\ModelUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Content Controller.
 */
class ContentController extends ActionController
{
    /**
     * Render the content Element via ExtBase.
     */
    public function indexAction()
    {
        try {
            $extensionKey = $this->settings['extensionKey'];
            $vendorName = $this->settings['vendorName'];
            $name = $this->settings['contentElement'];
            $data = $this->configurationManager->getContentObject()->data;

            $targetObject = ClassNamingUtility::getFqnByPath($vendorName, $extensionKey, 'Domain/Model/Content/' . $name);
            $model = ModelUtility::getModel($targetObject, $data);

            /** @var StandaloneView $view */
            $view = ExtendedUtility::create(StandaloneView::class);
            $context = $view->getRenderingContext();
            $context->setControllerName('Content');
            $context->setControllerAction($this->settings['contentElement']);
            $view->setRenderingContext($context);

            $configuration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
            $viewConfiguration = $configuration['view'];

            $layoutRootPaths = \is_array($viewConfiguration['layoutRootPaths']) ? $viewConfiguration['layoutRootPaths'] : [];
            $layoutRootPaths[5] = 'EXT:' . $this->settings['extensionKey'] . '/Resources/Private/Layouts/';
            $view->setLayoutRootPaths($layoutRootPaths);

            $partialRootPaths = \is_array($viewConfiguration['partialRootPaths']) ? $viewConfiguration['partialRootPaths'] : [];
            $partialRootPaths[5] = 'EXT:' . $this->settings['extensionKey'] . '/Resources/Private/Partials/';
            $view->setPartialRootPaths($partialRootPaths);

            $templateRootPaths = \is_array($viewConfiguration['templateRootPaths']) ? $viewConfiguration['templateRootPaths'] : [];
            $templateRootPaths[5] = 'EXT:' . $this->settings['extensionKey'] . '/Resources/Private/Templates/';
            $view->setTemplateRootPaths($templateRootPaths);

            $view->assignMultiple([
                'data' => $data,
                'object' => $model,
                'settings' => $this->settings,
            ]);

            return $view->render();
        } catch (\Exception $ex) {
            return 'Exception in content rendering: ' . $ex->getMessage();
        }
    }
}
