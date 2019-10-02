<?php

declare(strict_types=1);

/**
 * ReflectionService.
 */

namespace HDNET\Autoloader\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ReflectionService.
 *
 * For TYPO3 9 and higher
 */
class ReflectionService
{
    /**
     * Get the tag value
     * - Array (if the tag exist)
     * - false (if the tag do not exists).
     *
     * @param string $className
     * @param string $tag
     *
     * @return array|bool
     */
    public function getClassTagValues(string $className, string $tag)
    {
        try {
            $coreReflectionService = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Reflection\ReflectionService::class);
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
     * @param string $className
     * @param string $methodName
     *
     * @return array|bool
     */
    public function getMethodTagValues(string $className, string $methodName)
    {
        try {
            $coreReflectionService = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Reflection\ReflectionService::class);
            $classSchema = $coreReflectionService->getClassSchema($className);

            return $classSchema->getMethod($methodName)['tags'] ?? [];
        } catch (\Exception $e) {
            return false;
        }
    }
}
