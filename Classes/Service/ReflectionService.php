<?php

/**
 * ReflectionService
 */
namespace HDNET\Autoloader\Service;

use HDNET\Autoloader\Utility\ReflectionUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

/**
 * ReflectionService
 *
 * For TYPO3 9 and higher
 */
class ReflectionService
{

    /**
     * Get the tag value
     * - Array (if the tag exist)
     * - false (if the tag do not exists)
     *
     * @param string $className
     * @param string $tag
     * @return array|bool
     */
    public function getClassTagValues(string $className, string $tag)
    {
        try {
            $coreReflectionService = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Reflection\ReflectionService::class);
            $classSchema = $coreReflectionService->getClassSchema($className);
            $tags = $classSchema->getTags();

            if (!array_key_exists($tag, $tags)) {
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
     * - False (if there are any problems)
     *
     * @param string $className
     * @param string $methodName
     * @return array|bool
     */
    public function getMethodTagValues(string $className, string $methodName)
    {
        try {
            if ($this->is9orHigher()) {
                $coreReflectionService = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Reflection\ReflectionService::class);
                $classSchema = $coreReflectionService->getClassSchema($className);
                return $classSchema->getMethod($methodName)['tags'] ?? [];
            } else {
                $classReflection = ReflectionUtility::createReflectionClass($className);
                $methodReflection = $classReflection->getMethod($methodName);
                return $methodReflection->getTagsValues();
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Is 9 or higher.
     *
     * @return bool
     */
    public function is9orHigher(): bool
    {
        return VersionNumberUtility::convertVersionNumberToInteger(VersionNumberUtility::getCurrentTypo3Version()) >= VersionNumberUtility::convertVersionNumberToInteger('9.0.0');
    }
}
