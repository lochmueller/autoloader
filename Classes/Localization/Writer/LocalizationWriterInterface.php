<?php
/**
 * Interface for L10N file writers.
 */

namespace HDNET\Autoloader\Localization\Writer;

/**
 * Interface for L10N file writers.
 */
interface LocalizationWriterInterface
{
    /**
     * Get the base file content.
     *
     * @param string $extensionKey
     *
     * @return string
     */
    public function getBaseFileContent($extensionKey);

    /**
     * Get the absolute path to the file.
     *
     * @param string $extensionKey
     *
     * @return string
     */
    public function getAbsoluteFilename($extensionKey);

    /**
     * Add the label.
     *
     * @param string $extensionKey
     * @param string $key
     * @param string $default
     *
     * @return bool
     */
    public function addLabel($extensionKey, $key, $default);

    /**
     * Set language base name.
     *
     * @param string $languageBaseName
     */
    public function setLanguageBaseName($languageBaseName);
}
