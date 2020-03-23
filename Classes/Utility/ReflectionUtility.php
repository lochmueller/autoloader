<?php

/**
 * Reflection helper.
 */
declare(strict_types = 1);

namespace HDNET\Autoloader\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Reflection\ClassSchema;
use TYPO3\CMS\Extbase\Reflection\ReflectionService;

/**
 * Reflection helper.
 */
class ReflectionUtility
{
    /**
     * Check if the given class is instantiable.
     *
     * @param string $className
     */
    public static function isInstantiable($className): bool
    {
        $reflectionClass = new \ReflectionClass($className);

        return (bool)$reflectionClass->isInstantiable();
    }

    /**
     * Get the name of the parent class.
     *
     * @param string $className
     *
     * @return string
     */
    public static function getParentClassName($className)
    {
        $reflectionClass = new \ReflectionClass($className);

        return $reflectionClass->getParentClass()->getName();
    }

    /**
     * Check if the first class is found in the Hierarchy of the second.
     */
    public static function isClassInOtherClassHierarchy(string $searchClass, string $checkedClass): bool
    {
        $searchClass = trim($searchClass, '\\');
        if (!class_exists($searchClass)) {
            return false;
        }
        $checked = trim($checkedClass, '\\');

        try {
            if ($searchClass === $checked) {
                return true;
            }
            $reflection = new \ReflectionClass($searchClass);
            while ($reflection = $reflection->getParentClass()) {
                if ($checked === trim($reflection->getName(), '\\')) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Get first class tag information.
     * The trimmed value if the tag exists and FALSE if the tag do not exists.
     *
     * @return string|bool
     */
    public static function getFirstTagValue(string $className, string $tag)
    {
        $values = self::getClassTagValues($className, $tag);
        if (false === $values) {
            return false;
        }

        if (\is_array($values)) {
            return trim((string)$values[0]);
        }

        return false;
    }

    /**
     * Get the tag configuration from this method and respect multiple line and space configuration.
     *
     * @param string $className
     * @param        $methodName
     */
    public static function getTagConfigurationForMethod($className, $methodName, array $tagNames): array
    {
        $tags = self::getMethodTagValues($className, $methodName);

        $configuration = [];
        foreach ($tagNames as $tagName) {
            $configuration[$tagName] = [];
            if (!\is_array($tags[$tagName])) {
                continue;
            }
            foreach ($tags[$tagName] as $c) {
                $configuration[$tagName] = array_merge(
                    $configuration[$tagName],
                    GeneralUtility::trimExplode(' ', $c, true)
                );
            }
        }

        return $configuration;
    }

    /**
     * Get the tag configuration from this method and respect multiple line and space configuration.
     */
    public static function getTagConfigurationForClass(string $className, array $tagNames): array
    {
//        $classSchema = new ClassSchema($className);

        /** @todo */

        //       $reflectionService = $objectManager->get(ReflectionService::class);
        $tags = []; // $reflectionService->getClassTagsValues($className);

        $configuration = [];
        foreach ($tagNames as $tagName) {
            $configuration[$tagName] = [];
            if (!\is_array($tags[$tagName])) {
                continue;
            }
            foreach ($tags[$tagName] as $c) {
                $configuration[$tagName] = array_merge(
                    $configuration[$tagName],
                    GeneralUtility::trimExplode(' ', $c, true)
                );
            }
        }

        return $configuration;
    }

    /**
     * Get the tag configuration from this method and respect multiple line and space configuration.
     */
    public static function getTagConfigurationForProperty(string $className, string $property, array $tagNames): array
    {
        $coreReflectionService = GeneralUtility::makeInstance(ReflectionService::class);
        $classSchema = $coreReflectionService->getClassSchema($className);

        $tags = $classSchema->getProperty($property)['tags'];

        $configuration = [];
        foreach ($tagNames as $tagName) {
            if (!\is_array($tags[$tagName])) {
                continue;
            }
            $configuration[$tagName] = '';
            foreach ($tags[$tagName] as $c) {
                $configuration[$tagName] = trim($configuration[$tagName] . ' ' . $c);
            }
        }

        return $configuration;
    }

    /**
     * Get the tag value
     * - Array (if the tag exist)
     * - false (if the tag do not exists).
     *
     * @return array|bool
     */
    public static function getClassTagValues(string $className, string $tag)
    {
        try {
            $coreReflectionService = GeneralUtility::makeInstance(ReflectionService::class);
            $classSchema = $coreReflectionService->getClassSchema($className);
            $tags = $classSchema->getTags();

            if (!\array_key_exists($tag, $tags)) {
                return false;
            }

            return $tags[$tag] ?? [];
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get method tag values
     * - Array
     * - False (if there are any problems).
     *
     * @return array|bool
     */
    public static function getMethodTagValues(string $className, string $methodName)
    {
        try {
            $coreReflectionService = GeneralUtility::makeInstance(ReflectionService::class);
            $classSchema = $coreReflectionService->getClassSchema($className);

            return $classSchema->getMethod($methodName)['tags'] ?? [];
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get public method names.
     */
    public static function getPropertyNames(string $className): array
    {
        $coreReflectionService = GeneralUtility::makeInstance(ReflectionService::class);
        $classSchema = $coreReflectionService->getClassSchema($className);

        return array_keys($classSchema->getProperties());
    }

    /**
     * @throws \ReflectionException
     */
    public static function getPropertiesTaggedWith(string $className, string $tag): array
    {
        $classReflection = new \ReflectionClass($className);
        $props = $classReflection->getProperties();
        $result = [];
        foreach ($props as $prop) {
            /** @var \ReflectionProperty $prop */
            if (false !== mb_strpos((string)$prop->getDocComment(), '@' . $tag)) {
                $result[] = $prop->getName();
            }
        }

        return $result;
    }

    /**
     * Get properties of the given class, that are als declared in the given class.
     *
     * @return array
     */
    public static function getDeclaringProperties(string $className)
    {
        $classReflection = new \ReflectionClass($className);
        $own = array_filter($classReflection->getProperties(), function ($property) use ($className) {
            return trim((string)$property->class, '\\') === trim($className, '\\');
        });

        return array_map(function ($item) {
            return (string)$item->name;
        }, $own);
    }

    /**
     * Check if the method is tagged with the given tag (no value checked).
     *
     * @param string $className
     * @param string $methodName
     * @param string $tagName
     */
    public static function isMethodTaggedWith($className, $methodName, $tagName): bool
    {
        $tags = self::getMethodTagValues($className, $methodName);

        return \array_key_exists($tagName, $tags);
    }

    /**
     * Check if the property is tagged with the given tag (no value checked).
     *
     * @param string $className
     * @param string $propertyName
     * @param string $tagName
     */
    public static function isPropertyTaggedWith($className, $propertyName, $tagName): bool
    {
        $properties = self::getPropertiesTaggedWith($className, $tagName);

        return \in_array($propertyName, $properties, true);
    }
}
