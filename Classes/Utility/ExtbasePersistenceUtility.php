<?php

declare(strict_types=1);

namespace HDNET\Autoloader\Utility;

use HDNET\Autoloader\SmartObjectRegister;

class ExtbasePersistenceUtility
{
    /**
     * @return mixed[]
     */
    public static function getClassMappingForExtension(string $extension, array $additionalConfiguration = []): array
    {
        $objects = self::getSmartObjectsForExtensionKey($extension);
        $baseConfiguration = self::generateAutoloaderConfiguration($objects);

        return array_merge($baseConfiguration, $additionalConfiguration);
    }

    /**
     * @param mixed $extensionKey
     *
     * @return mixed[]
     */
    protected static function getSmartObjectsForExtensionKey($extensionKey): array
    {
        $smartObjects = SmartObjectRegister::getRegister();
        $extensionObjects = [];
        foreach ($smartObjects as $className) {
            $objectExtension = ClassNamingUtility::getExtensionKeyByModel($className);
            if ($objectExtension === $extensionKey) {
                $extensionObjects[] = $className;
            }
        }

        return $extensionObjects;
    }

    /**
     * @return array<int|string, array<string, string>>
     */
    private static function generateAutoloaderConfiguration(array $objects): array
    {
        $config = [];
        foreach ($objects as $className) {
            $table = ModelUtility::getTableNameByModelReflectionAnnotation($className);
            if ('' !== $table) {
                $config[$className] = [
                    'tableName' => $table,
                ];
            }
        }

        return $config;
    }
}
