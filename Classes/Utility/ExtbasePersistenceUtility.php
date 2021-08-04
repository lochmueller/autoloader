<?php

declare(strict_types=1);

namespace HDNET\Autoloader\Utility;

use HDNET\Autoloader\SmartObjectRegister;

class ExtbasePersistenceUtility
{
    public static function getClassMappingForExtension(string $extension, array $additionalConfiguration = []): array
    {
        $objects = self::getSmartObjectsForExtensionKey($extension);
        $baseConfiguration = self::generateAutoloaderConfiguration($objects);

        return array_merge($baseConfiguration, $additionalConfiguration);
    }

    protected static function getSmartObjectsForExtensionKey($extensionKey)
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

    private static function generateAutoloaderConfiguration(array $objects)
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
