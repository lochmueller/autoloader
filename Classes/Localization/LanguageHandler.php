<?php

/**
 * Handling of the language files.
 */
declare(strict_types = 1);

namespace HDNET\Autoloader\Localization;

use HDNET\Autoloader\Localization\Writer\AbstractLocalizationWriter;
use TYPO3\CMS\Core\Localization\LanguageStore;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Handling of the language files.
 */
class LanguageHandler extends LanguageStore
{
    /**
     * Cache the created labels.
     *
     * @var array
     */
    protected static $createdLabelCache = [];

    /**
     * handler the adding of files.
     *
     * @param string $key                  key in the localization file
     * @param string $extensionName
     * @param string $default              default value of the label
     * @param array  $arguments            arguments are being passed over to vsprintf
     * @param string $overrideLanguageBase
     *
     * @return string|null
     */
    public function handle($key, $extensionName, $default, $arguments, $overrideLanguageBase = null)
    {
        // If we are called early in the TYPO3 bootstrap we mus return early with the default label
        if (empty($GLOBALS['TCA'])) {
            return $default;
        }
        $value = LocalizationUtility::translate($key, $extensionName, $arguments);

        if (null !== $value) {
            return $value;
        }

        if (null === $default || '' === $default) {
            $default = $extensionName . ' ==> LLL:' . $key;
        }

        $handler = $this->getBestLanguageWriter($extensionName, $overrideLanguageBase);
        $handler->createFileIfNotExists($extensionName);

        $labelCacheKey = $extensionName . '|' . $key;
        if (!\in_array($labelCacheKey, self::$createdLabelCache, true)) {
            $handler->addLabel($extensionName, $key, $default);
            self::$createdLabelCache[] = $labelCacheKey;
        }

        return $default;
    }

    /**
     * Get the best language writer.
     *
     * @param string $extensionKey
     * @param string $overrideLanguageBase
     *
     * @return AbstractLocalizationWriter
     */
    protected function getBestLanguageWriter($extensionKey, $overrideLanguageBase = null)
    {
        $services = [];
        foreach ($this->getSupportedExtensions() as $serviceKey) {
            if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['lang']['writer'][$serviceKey])) {
                continue;
            }
            $serviceName = $GLOBALS['TYPO3_CONF_VARS']['SYS']['lang']['writer'][$serviceKey];

            /** @var AbstractLocalizationWriter $service */
            $service = GeneralUtility::makeInstance($serviceName);
            if (null !== $overrideLanguageBase) {
                $service->setLanguageBaseName($overrideLanguageBase);
            }
            if (is_file($service->getAbsoluteFilename($extensionKey))) {
                return $service;
            }
            $services[] = $service;
        }

        return $services[0];
    }
}
