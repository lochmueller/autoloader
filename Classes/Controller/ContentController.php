<?php

/**
 * Content Controller.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Controller;

use HDNET\Autoloader\Utility\ClassNamingUtility;
use HDNET\Autoloader\Utility\ExtendedUtility;
use HDNET\Autoloader\Utility\ModelUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Frontend\ContentObject\ContentDataProcessor;

/**
 * Content Controller.
 */
class ContentController extends ActionController
{
    /**
     * Render the content Element via ExtBase.
     */
    public function indexAction(): string
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

            /** @var ContentDataProcessor $contentDataProcessor */
            $contentDataProcessor = GeneralUtility::makeInstance(ContentDataProcessor::class);
            $variables = $contentDataProcessor->process(
                $this->configurationManager->getContentObject(),
                ['dataProcessing.' => $this->settings['dataProcessing'] ?? null],
                ['data' => $data]
            );
            $variables['settings'] = $this->settings;
            $variables['object'] = $model;

            $viewConfiguration = $configuration['view'];

            $layoutRootPaths = \is_array($viewConfiguration['layoutRootPaths']) ? $viewConfiguration['layoutRootPaths'] : [];
            if (!isset($layoutRootPaths[5])) {
                $layoutRootPaths[5] = 'EXT:' . $this->settings['extensionKey'] . '/Resources/Private/Layouts/';
            }
            $view->setLayoutRootPaths($layoutRootPaths);

            $partialRootPaths = \is_array($viewConfiguration['partialRootPaths']) ? $viewConfiguration['partialRootPaths'] : [];
            if (!isset($partialRootPaths[5])) {
                $partialRootPaths[5] = 'EXT:' . $this->settings['extensionKey'] . '/Resources/Private/Partials/';
            }
            $view->setPartialRootPaths($partialRootPaths);

            $templateRootPaths = \is_array($viewConfiguration['templateRootPaths']) ? $viewConfiguration['templateRootPaths'] : [];
            if (!isset($templateRootPaths[5])) {
                $templateRootPaths[5] = 'EXT:' . $this->settings['extensionKey'] . '/Resources/Private/Templates/';
            }
            $view->setTemplateRootPaths($templateRootPaths);

            $view->assignMultiple(
                $variables
            );

            return $view->render();
        } catch (\Exception $ex) {
            return 'Exception in content rendering: ' . $ex->getMessage();
        }
    }
}
