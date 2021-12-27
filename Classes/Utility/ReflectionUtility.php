<?php

/**
 * Reflection helper.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Utility;

use Doctrine\Common\Annotations\AnnotationReader;
use HDNET\Autoloader\Annotation\NoHeader;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Reflection helper.
 */
class ReflectionUtility
{
    /**
     * Check if the given class is instantiable.
     */
    public static function isInstantiable(string $className): bool
    {
        $reflectionClass = new \ReflectionClass($className);

        return (bool)$reflectionClass->isInstantiable();
    }

    /**
     * Get the name of the parent class.
     *
     * @return string
     */
    public static function getParentClassName(string $className)
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
     * Get properties of the given class, that are als declared in the given class.
     *
     * @return string[]
     */
    public static function getDeclaringProperties(string $className): array
    {
        $classReflection = new \ReflectionClass($className);
        $own = array_filter($classReflection->getProperties(), function ($property) use ($className): bool {
            return trim((string)$property->class, '\\') === trim($className, '\\');
        });

        return array_map(function ($item): string {
            return (string)$item->name;
        }, $own);
    }

    /**
     * Check if the class is tagged with noHeader.
     *
     * @param $class
     */
    public static function isTaggedWithNoHeader($class): bool
    {
        /** @var AnnotationReader $annotationReader */
        $annotationReader = GeneralUtility::makeInstance(AnnotationReader::class);

        $classNameRef = new \ReflectionClass($class);

        return null !== $annotationReader->getClassAnnotation($classNameRef, NoHeader::class);
    }
}
