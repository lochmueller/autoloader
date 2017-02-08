<?php
/**
 * Icon helper
 *
 * @author Tim Lochmüller
 */

namespace HDNET\Autoloader\Utility;

use TYPO3\CMS\Backend\Sprite\SpriteManager;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

/**
 * Icon helper
 */
class IconUtility
{

    /**
     * Add the given icon to the TCA table type
     *
     * @param string $table
     * @param string $type
     * @param string $icon
     */
    public static function addTcaTypeIcon($table, $type, $icon)
    {
        if (ExtendedUtility::isBranchActive(7)) {
            $fullIconPath = substr(PathUtility::getAbsoluteWebPath($icon), 1);
            if (StringUtility::endsWith(strtolower($fullIconPath), 'svg')) {
                $iconProviderClass = 'TYPO3\\CMS\\Core\\Imaging\\IconProvider\\SvgIconProvider';
            } else {
                $iconProviderClass = 'TYPO3\\CMS\\Core\\Imaging\\IconProvider\\BitmapIconProvider';
            }
            /** @var IconRegistry $iconRegistry */
            $iconRegistry = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Imaging\\IconRegistry');
            $iconIdentifier = 'tcarecords-' . $table . '-' . $type;
            $iconRegistry->registerIcon($iconIdentifier, $iconProviderClass, ['source' => $fullIconPath]);
            $GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes'][$type] = $iconIdentifier;
        } else {
            $fullIconPath = str_replace('../typo3/', '', $icon);
            SpriteManager::addTcaTypeIcon('tt_content', $type, $fullIconPath);
        }
    }

    /**
     * Get the relative path of the extension icon
     *
     * @param string $extensionKey
     * @param boolean $extSyntax Get the EXT: Syntax instead of a rel Path
     *
     * @return string
     */
    public static function getByExtensionKey($extensionKey, $extSyntax = false)
    {
        $extPath = ExtensionManagementUtility::extPath($extensionKey) . 'ext_icon.';
        $fileExtension = self::getIconFileExtension($extPath);
        if ($fileExtension) {
            return self::returnRelativeIconPath($extensionKey, 'ext_icon.' . $fileExtension, $extSyntax);
        }
        return self::getByExtensionKey('autoloader');
    }

    /**
     * Get the absolute table icon for the given model name
     *
     * @param string $modelClassName
     * @param boolean $extSyntax Get the EXT: Syntax instead of a rel Path
     *
     * @return string
     */
    public static function getByModelName($modelClassName, $extSyntax = false)
    {
        $modelInformation = ClassNamingUtility::explodeObjectModelName($modelClassName);

        $extensionKey = GeneralUtility::camelCaseToLowerCaseUnderscored($modelInformation['extensionName']);
        $modelName = str_replace('\\', '/', $modelInformation['modelName']);

        $tableIconPath = ExtensionManagementUtility::extPath($extensionKey) . 'Resources/Public/Icons/' . $modelName . '.';
        $fileExtension = self::getIconFileExtension($tableIconPath);
        if ($fileExtension) {
            return self::returnRelativeIconPath(
                $extensionKey,
                'Resources/Public/Icons/' . $modelName . '.' . $fileExtension,
                $extSyntax
            );
        }
        return self::getByExtensionKey($extensionKey, $extSyntax);
    }

    /**
     * Get the file extension (svg,png,gif) of the absolute path. The path is
     * without file extension but incl. the dot. e.g.:
     * "/test/icon."
     *
     * @param string $absolutePathWithoutExtension
     *
     * @return string
     */
    public static function getIconFileExtension($absolutePathWithoutExtension)
    {
        $fileExtensions = [
            'svg',
            'png',
            'gif',
            'jpg',
        ];
        foreach ($fileExtensions as $fileExtension) {
            if (is_file($absolutePathWithoutExtension . $fileExtension)) {
                return $fileExtension;
            }
        }
        return false;
    }

    /**
     * Return the right relative path
     *
     * @param string $extensionKey
     * @param string $path
     * @param boolean $extSyntax
     *
     * @return string
     */
    protected static function returnRelativeIconPath($extensionKey, $path, $extSyntax = false)
    {
        if ($extSyntax) {
            return 'EXT:' . $extensionKey . '/' . $path;
        }
        return ExtensionManagementUtility::extRelPath($extensionKey) . $path;
    }
}
