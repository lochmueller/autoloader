<?php
/**
 * Reflection helper
 *
 * @author Tim Lochmüller
 */

namespace HDNET\Autoloader\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Reflection\ClassReflection;
use TYPO3\CMS\Extbase\Reflection\MethodReflection;

/**
 * Reflection helper
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
     */
    static public function createReflectionClass($className)
    {
        return new ClassReflection($className);
    }

    /**
     * Check if the given class is instantiable
     *
     * @param string $className
     *
     * @return bool
     */
    static public function isInstantiable($className)
    {
        return self::createReflectionClass($className)
            ->isInstantiable();
    }

    /**
     * Get the name of the parent class
     *
     * @param string $className
     *
     * @return string
     */
    static public function getParentClassName($className)
    {
        return self::createReflectionClass($className)
            ->getParentClass()
            ->getName();
    }

    /**
     * Get all properties that are tagged with the given tag
     *
     * @param string $className
     * @param string $tag
     *
     * @return array
     */
    static public function getPropertiesTaggedWith($className, $tag)
    {
        $classReflection = self::createReflectionClass($className);
        $properties = [];
        foreach ($classReflection->getProperties() as $property) {
            /** @var \TYPO3\CMS\Extbase\Reflection\PropertyReflection $property */
            if ($property->isTaggedWith($tag)) {
                $properties[] = $property;
            }
        }
        return $properties;
    }

    /**
     * Get all public methods of the given class
     *
     * @param string $className
     *
     * @return MethodReflection
     */
    static public function getPublicMethods($className)
    {
        return self::createReflectionClass($className)
            ->getMethods(\ReflectionMethod::IS_PUBLIC);
    }

    /**
     * Get first class tag information.
     * The trimmed value if the tag exists and FALSE if the tag do not exists
     *
     * @param string $className
     * @param string $tag
     *
     * @return string|bool
     */
    static public function getFirstTagValue($className, $tag)
    {
        $classReflection = self::createReflectionClass($className);
        if (!$classReflection->isTaggedWith($tag)) {
            return false;
        }
        $values = $classReflection->getTagValues($tag);
        if (is_array($values)) {
            return trim($values[0]);
        }
        return false;
    }

    /**
     * Get the tag configuration from this method and respect multiple line and space configuration
     *
     * @param MethodReflection|ClassReflection $reflectionObject
     * @param array                            $tagNames
     *
     * @return array
     */
    static public function getTagConfiguration($reflectionObject, array $tagNames)
    {
        $tags = $reflectionObject->getTagsValues();
        $configuration = [];
        foreach ($tagNames as $tagName) {
            $configuration[$tagName] = [];
            if (!is_array($tags[$tagName])) {
                continue;
            }
            foreach ($tags[$tagName] as $c) {
                $configuration[$tagName] = array_merge($configuration[$tagName], GeneralUtility::trimExplode(' ', $c, true));
            }
        }
        return $configuration;
    }

    /**
     * Get properties of the given class, that are als declared in the given class
     *
     * @param string $className
     *
     * @return array
     */
    static public function getDeclaringProperties($className)
    {
        $properties = [];
        $classReflection = self::createReflectionClass($className);
        foreach ($classReflection->getProperties() as $property) {
            /** @var \TYPO3\CMS\Extbase\Reflection\PropertyReflection $property */
            if ($property->getDeclaringClass()
                    ->getName() === $classReflection->getName()
            ) {
                $properties[] = $property->getName();
            }
        }
        return $properties;
    }

}
