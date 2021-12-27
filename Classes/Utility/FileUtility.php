<?php

/**
 * FileUtility.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Utility;

use HDNET\Autoloader\Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

/**
 * FileUtility.
 */
class FileUtility
{
    /**
     * Write a file and create the target folder, if the folder do not exists.
     *
     * @throws Exception
     */
    public static function writeFileAndCreateFolder(string $absoluteFileName, string $content): bool
    {
        $dir = PathUtility::dirname($absoluteFileName) . '/';
        if (!is_dir($dir)) {
            GeneralUtility::mkdir_deep($dir);
        }
        if (is_file($absoluteFileName) && !is_writable($absoluteFileName)) {
            throw new Exception('The autoloader try to add same content to ' . $absoluteFileName . ' but the file is not writable for the autoloader. Please fix it!', 234627835);
        }

        return GeneralUtility::writeFile($absoluteFileName, $content);
    }

    /**
     * Get all base file names in the given directory with the given file extension
     * Check also if the directory exists.
     *
     * @return mixed[]
     */
    public static function getBaseFilesInDir(string $dirPath, string $fileExtension): array
    {
        return self::getFileInformationInDir($dirPath, $fileExtension, PATHINFO_FILENAME);
    }

    /**
     * Get all base file names in the given directory with the given file extension
     * Check also if the directory exists.
     *
     * @return mixed[]
     */
    public static function getBaseFilesWithExtensionInDir(string $dirPath, string $fileExtension): array
    {
        return self::getFileInformationInDir($dirPath, $fileExtension, PATHINFO_BASENAME);
    }

    /**
     * Get all base file names in the given directory with the given file extension
     * Check also if the directory exists. If you scan the dir recursively you get
     * also the folder name. The filename is also "basename" only.
     *
     * @return mixed[]
     */
    public static function getBaseFilesRecursivelyInDir(string $dirPath, string $fileExtensions, bool $recursively = true): array
    {
        if (!is_dir($dirPath)) {
            return [];
        }
        $recursively = $recursively ? 99 : 0;
        $files = GeneralUtility::getAllFilesAndFoldersInPath([], $dirPath, $fileExtensions, false, $recursively);
        foreach ($files as $key => $file) {
            $pathInfo = PathUtility::pathinfo($file);
            $files[$key] = $pathInfo['dirname'] . '/' . $pathInfo['filename'];
        }
        $files = GeneralUtility::removePrefixPathFromList($files, $dirPath);

        return array_values($files);
    }

    /**
     * Get file information in the given folder.
     *
     * @return mixed[]
     */
    protected static function getFileInformationInDir(string $dirPath, string $fileExtension, int $informationType): array
    {
        if (!is_dir($dirPath)) {
            return [];
        }
        $files = GeneralUtility::getFilesInDir($dirPath, $fileExtension);
        foreach ($files as $key => $file) {
            $files[$key] = PathUtility::pathinfo($file, $informationType);
        }

        return array_values($files);
    }
}
