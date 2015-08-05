<?php
/**
 * Interface for L10N file writers
 *
 * @author  Tim Lochmüller
 */

namespace HDNET\Autoloader\Localization\Writer;

/**
 * Interface for L10N file writers
 */
interface LocalizationWriterInterface {

	/**
	 * Get the base file content
	 *
	 * @return string
	 */
	public function getBaseFileContent();

	/**
	 * Get the absolute path to the file
	 *
	 * @param string $extensionKey
	 *
	 * @return string
	 */
	public function getAbsoluteFilename($extensionKey);

	/**
	 * Add the label
	 *
	 * @param string $extensionKey
	 * @param string $key
	 * @param string $default
	 *
	 * @return bool
	 */
	public function addLabel($extensionKey, $key, $default);
}
