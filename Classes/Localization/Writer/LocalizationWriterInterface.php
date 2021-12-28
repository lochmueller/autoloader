<?php

/**
 * Interface for L10N file writers.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Localization\Writer;

/**
 * Interface for L10N file writers.
 */
interface LocalizationWriterInterface
{
    /**
     * Get the base file content.
     *
     * @return string
     */
    public function getBaseFileContent(string $extensionKey);

    /**
     * Get the absolute path to the file.
     *
     * @return string
     */
    public function getAbsoluteFilename(string $extensionKey);

    /**
     * Add the label.
     *
     * @return bool
     */
    public function addLabel(string $extensionKey, string $key, string $default);

    /**
     * Set language base name.
     */
    public function setLanguageBaseName(string $languageBaseName);
}
