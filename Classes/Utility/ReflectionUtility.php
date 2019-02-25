<?php

/**
 * Reflection helper.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Reflection\ClassReflection;
use TYPO3\CMS\Extbase\Reflection\ReflectionService;

/**
 * Reflection helper.
 */
class ReflectionUtility
{
    /**
     * Create a new class reflection. Do not use the makeInstance or objectManager
     * because the reflection API is also used in front of the caching framework.
     *
     * @param string $className
     *
     * @return ClassReflection
     *
     * @deprecated
     */
    public static function createReflectionClass($className)
    {
        return new ClassReflection($className);
    }

    /**
     * Check if the given class is instantiable.
     *
     * @param string $className
     *
     * @return bool
     */
    public static function isInstantiable($className): bool
    {
        $reflectionClass = new \ReflectionClass($className);

        return (bool) $reflectionClass->isInstantiable();
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
        if (self::is9orHigher()) {
            $reflectionClass = new \ReflectionClass($className);

            return $reflectionClass->getParentClass()->getName();
        }

        return self::createReflectionClass($className)
            ->getParentClass()
            ->getName();
    }

    /**
     * Get first class tag information.
     * The trimmed value if the tag exists and FALSE if the tag do not exists.
     *
     * @param string $className
     * @param string $tag
     *
     * @return string|bool
     */
    public static function getFirstTagValue(string $className, string $tag)
    {
        if (self::is9orHigher()) {
            $reflectionService = GeneralUtility::makeInstance(\HDNET\Autoloader\Service\ReflectionService::class);
            $values = $reflectionService->getClassTagValues($className, $tag);
            if ($values === false) {
                return false;
            }
        } else {
            $classReflection = self::createReflectionClass($className);
            if (!$classReflection->isTaggedWith($tag)) {
                return false;
            }
            $values = $classReflection->getTagValues($tag);
        }

        if (\is_array($values)) {
            return \trim((string) $values[0]);
        }

        return false;
    }

    /**
     * Get the tag configuration from this method and respect multiple line and space configuration.
     *
     * @param string $className
     * @param        $methodName
     * @param array  $tagNames
     *
     * @return array
     */
    public static function getTagConfigurationForMethod($className, $methodName, array $tagNames): array
    {
        $reflectionService = GeneralUtility::makeInstance(\HDNET\Autoloader\Service\ReflectionService::class);
        $tags = $reflectionService->getMethodTagValues($className, $methodName);

        $configuration = [];
        foreach ($tagNames as $tagName) {
            $configuration[$tagName] = [];
            if (!\is_array($tags[$tagName])) {
                continue;
            }
            foreach ($tags[$tagName] as $c) {
                $configuration[$tagName] = \array_merge(
                    $configuration[$tagName],
                    GeneralUtility::trimExplode(' ', $c, true)
                );
            }
        }

        return $configuration;
    }

    /**
     * Get the tag configuration from this method and respect multiple line and space configuration.
     *
     * @param string $className
     * @param array  $tagNames
     *
     * @return array
     */
    public static function getTagConfigurationForClass(string $className, array $tagNames): array
    {
        $reflectionService = self::getReflectionService();
        $tags = $reflectionService->getClassTagsValues($className);

        $configuration = [];
        foreach ($tagNames as $tagName) {
            $configuration[$tagName] = [];
            if (!\is_array($tags[$tagName])) {
                continue;
            }
            foreach ($tags[$tagName] as $c) {
                $configuration[$tagName] = \array_merge(
                    $configuration[$tagName],
                    GeneralUtility::trimExplode(' ', $c, true)
                );
            }
        }

        return $configuration;
    }

    /**
     * Get the tag configuration from this method and respect multiple line and space configuration.
     *
     * @param string $className
     * @param string $property
     * @param array  $tagNames
     *
     * @return array
     */
    public static function getTagConfigurationForProperty(string $className, string $property, array $tagNames): array
    {
        $reflectionService = self::getReflectionService();
        if (self::is9orHigher()) {
            $tags = $reflectionService->getClassSchema($className)->getProperty($property)['tags'];
        } else {
            $tags = $reflectionService->getPropertyTagsValues($className, $property);
        }

        $configuration = [];
        foreach ($tagNames as $tagName) {
            if (!\is_array($tags[$tagName])) {
                continue;
            }
            $configuration[$tagName] = '';
            foreach ($tags[$tagName] as $c) {
                $configuration[$tagName] = \trim($configuration[$tagName] . ' ' . $c);
            }
        }

        return $configuration;
    }

    /**
     * Get public method names.
     *
     * @param string $className
     *
     * @return array
     */
    public static function getPropertyNames(string $className): array
    {
        $reflectionService = self::getReflectionService();

        return \array_keys($reflectionService->getClassSchema($className)->getProperties());
    }

    /**
     * @param string $className
     * @param string $tag
     *
     * @throws \ReflectionException
     *
     * @return array
     */
    public static function getPropertiesTaggedWith(string $className, string $tag): array
    {
        $classReflection = new \ReflectionClass($className);
        $props = $classReflection->getProperties();
        $result = [];
        foreach ($props as $prop) {
            /** @var $prop \ReflectionProperty */
            if (false !== \mb_strpos($prop->getDocComment(), '@' . $tag)) {
                $result[] = $prop->getName();
            }
        }

        return $result;
    }

    /**
     * Get public method names.
     *
     * @param string $className
     *
     * @return array
     */
    public static function getPublicMethodNames(string $className): array
    {
        $methodNames = [];

        if (self::is9orHigher()) {
            $reflectionService = self::getReflectionService();
            $schema = $reflectionService->getClassSchema($className);
            $methods = $schema->getMethods();
            foreach ($methods as $key => $method) {
                if ($method['public']) {
                    $methodNames[] = $key;
                }
            }

            return $methodNames;
        }

        $methods = self::createReflectionClass($className)
            ->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            $methodNames[] = $method->getName();
        }

        return $methodNames;
    }

    /**
     * Get properties of the given class, that are als declared in the given class.
     *
     * @param string $className
     *
     * @return array
     */
    public static function getDeclaringProperties(string $className)
    {
        $classReflection = new \ReflectionClass($className);
        $own = \array_filter($classReflection->getProperties(), function ($property) use ($className) {
            return \trim((string) $property->class, '\\') === \trim($className, '\\');
        });

        return \array_map(function ($item) {
            return (string) $item->name;
        }, $own);
    }

    /**
     * Is 9 or higher.
     *
     * @return bool
     */
    public static function is9orHigher(): bool
    {
        return VersionNumberUtility::convertVersionNumberToInteger(TYPO3_branch) >= VersionNumberUtility::convertVersionNumberToInteger('9.0');
    }

    /**
     * Check if the method is tagged with the given tag (no value checked).
     *
     * @param string $className
     * @param string $methodName
     * @param string $tagName
     *
     * @return bool
     */
    public static function isMethodTaggedWith($className, $methodName, $tagName): bool
    {
        $reflectionService = GeneralUtility::makeInstance(\HDNET\Autoloader\Service\ReflectionService::class);
        $tags = $reflectionService->getMethodTagValues($className, $methodName);

        return \array_key_exists($tagName, $tags);
    }

    /**
     * Check if the property is tagged with the given tag (no value checked).
     *
     * @param string $className
     * @param string $propertyName
     * @param string $tagName
     *
     * @return bool
     */
    public static function isPropertyTaggedWith($className, $propertyName, $tagName): bool
    {
        $properties = self::getPropertiesTaggedWith($className, $tagName);

        return \in_array($propertyName, $properties, true);
    }

    /**
     * Create reflection service.
     *
     * @return ReflectionService
     */
    protected static function getReflectionService()
    {
        $objectManager = new ObjectManager();

        return $objectManager->get(ReflectionService::class);
    }
}
