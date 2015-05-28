<?php
/**
 * FileUtility
 *
 * @category Extension
 * @package  Autoloader\Utility
 * @author   Tim Lochmüller
 */

namespace HDNET\Autoloader\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

/**
 * FileUtility
 *
 * @author Tim Lochmüller
 */
class FileUtility {

	/**
	 * Write a file and create the target folder, if the folder do not exists
	 *
	 * @param string $absoluteFileName
	 * @param string $content
	 *
	 * @return bool
	 */
	static public function writeFileAndCreateFolder($absoluteFileName, $content) {
		$dir = PathUtility::dirname($absoluteFileName) . '/';
		if (!is_dir($dir)) {
			GeneralUtility::mkdir_deep($dir);
		}
		return GeneralUtility::writeFile($absoluteFileName, $content);
	}

	/**
	 * Get all base file names in the given directory with the given file extension
	 * Check also if the directory exists
	 *
	 * @param string $dirPath
	 * @param string $fileExtension
	 *
	 * @return array
	 */
	static public function getBaseFilesInDir($dirPath, $fileExtension) {
		if (!is_dir($dirPath)) {
			return array();
		}
		$files = GeneralUtility::getFilesInDir($dirPath, $fileExtension);
		foreach ($files as $key => $file) {
			$files[$key] = PathUtility::pathinfo($file, PATHINFO_FILENAME);
		}
		return array_values($files);
	}

	/**
	 * Get all base file names in the given directory with the given file extension
	 * Check also if the directory exists
	 *
	 * @param string $dirPath
	 * @param string $fileExtension
	 *
	 * @return array
	 */
	static public function getBaseFilesWithExtensionInDir($dirPath, $fileExtension) {
		if (!is_dir($dirPath)) {
			return array();
		}
		$files = GeneralUtility::getFilesInDir($dirPath, $fileExtension);
		foreach ($files as $key => $file) {
			$files[$key] = PathUtility::pathinfo($file, PATHINFO_BASENAME);
		}
		return array_values($files);
	}

	/**
	 * Get all base file names in the given directory with the given file extension
	 * Check also if the directory exists. If you scan the dir recursively you get
	 * also the folder name. The filename is also "basename" only.
	 *
	 * @param string $dirPath
	 * @param string $fileExtension
	 * @param bool   $recursively
	 *
	 * @return array
	 * @todo migrate this into the getBaseFilesInDir or rethink the fetch handling
	 */
	static public function getBaseFilesRecursivelyInDir($dirPath, $fileExtension, $recursively = TRUE) {
		if (!is_dir($dirPath)) {
			return array();
		}
		$recursively = $recursively ? 99 : 0;
		$files = GeneralUtility::getAllFilesAndFoldersInPath(array(), $dirPath, $fileExtension, FALSE, $recursively);
		foreach ($files as $key => $file) {
			$pathInfo = PathUtility::pathinfo($file);
			$files[$key] = $pathInfo['dirname'] . '/' . $pathInfo['filename'];
		}
		$files = GeneralUtility::removePrefixPathFromList($files, $dirPath);
		return array_values($files);
	}
}