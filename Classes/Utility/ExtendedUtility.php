<?php

/**
 * Utility functions for the Autoloader.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Utility functions for the Autoloader.
 */
class ExtendedUtility
{
    /**
     * Create a object with the given class name.
     */
    public static function create(string $className): object
    {
        $arguments = \func_get_args();
        $objManager = GeneralUtility::makeInstance(ObjectManager::class);

        return $objManager->get($className);
    }

    /**
     * Get the query for the given class name oder object.
     *
     * @param object|string $objectName
     */
    public static function getQuery($objectName): QueryInterface
    {
        $objectName = \is_object($objectName) ? \get_class($objectName) : $objectName;
        /** @var PersistenceManagerInterface $manager */
        static $manager = null;
        if (null === $manager) {
            $manager = self::create(PersistenceManagerInterface::class);
        }

        return $manager->createQueryForType($objectName);
    }

    /**
     * Add a xclass/object replacement.
     *
     * @param $source
     * @param $target
     */
    public static function addXclass($source, $target): bool
    {
        if (isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][$source])) {
            $message = 'Double registration of Xclass for ' . $source;
            $message .= ' (' . $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][$source]['className'] . ' and ' . $target . ')';
            self::log($message);

            return false;
        }
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][$source] = [
            'className' => $target,
        ];

        return true;
    }

    /**
     * Log into the TYPO3_CONF_VARS to get more information in the backend.
     *
     * @param $message
     */
    public static function log($message): void
    {
        if (!\is_array($GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['Log'])) {
            $GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['Log'] = [];
        }
        $GLOBALS['TYPO3_CONF_VARS']['AUTOLOADER']['Log'][] = $message;
    }

    /**
     * Add a hooks.
     */
    public static function addHooks(array $locations, string $configuration): void
    {
        foreach ($locations as $location) {
            self::addHook($location, $configuration);
        }
    }

    /**
     * Add a hook.
     *
     * @param string $location The location of the hook separated bei pipes
     */
    public static function addHook(string $location, string $configuration): void
    {
        $location = explode('|', $location);
        $location[] = 'via_autoloader_'.substr(md5($configuration), 0, 10);
        ArrayUtility::setNodes([implode('|', $location) => $configuration], $GLOBALS);
    }

    /**
     * Create a StandaloneView for a extension context.
     */
    public static function createExtensionStandaloneView(string $extensionKey, string $templatePath): StandaloneView
    {
        $templatePath = GeneralUtility::getFileAbsFileName($templatePath);

        /** @var StandaloneView $view */
        $view = self::create(StandaloneView::class);
        $view->setTemplatePathAndFilename($templatePath);

        // Get configuration
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var ConfigurationManager $configurationManager */
        $configurationManager = $objectManager->get(ConfigurationManager::class);
        $configuration = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $viewConfiguration = [];
        if (isset($configuration['module.']['tx_autoloader.']['view.'])) {
            $viewConfiguration = $configuration['module.']['tx_autoloader.']['view.'];
        }

        $layoutRootPaths = \is_array($viewConfiguration['layoutRootPaths.']) ? $viewConfiguration['layoutRootPaths.'] : [];
        if (!isset($layoutRootPaths[5])) {
            $layoutRootPaths[5] = 'EXT:' . $extensionKey . '/Resources/Private/Layouts/';
        }
        $view->setLayoutRootPaths($layoutRootPaths);

        $partialRootPaths = \is_array($viewConfiguration['partialRootPaths.']) ? $viewConfiguration['partialRootPaths.'] : [];
        if (!isset($partialRootPaths[5])) {
            $partialRootPaths[5] = 'EXT:' . $extensionKey . '/Resources/Private/Partials/';
        }
        $view->setPartialRootPaths($partialRootPaths);

        return $view;
    }
}
