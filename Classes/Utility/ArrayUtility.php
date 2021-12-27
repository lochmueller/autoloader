<?php

/**
 * Arrays utility.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Utility;

/**
 * Arrays utility.
 */
class ArrayUtility
{
    /**
     * Set a node in the array.
     *
     * @see http://www.php.net/manual/de/function.array-walk-recursive.php#106340
     */
    public static function setNodes(array $data, array &$array): void
    {
        $separator = '|';
        foreach ($data as $name => $value) {
            if (false === mb_strpos($name, $separator)) {
                $array[$name] = $value;
            } else {
                $keys = explode($separator, $name);
                $optTree = &$array;
                while (($key = array_shift($keys))) {
                    if ([] !== $keys) {
                        if (!isset($optTree[$key]) || !\is_array($optTree[$key])) {
                            $optTree[$key] = [];
                        }
                        $optTree = &$optTree[$key];
                    } else {
                        $optTree[$key] = $value;
                    }
                }
            }
        }
    }

    /**
     * Merge the Array Smart.
     *
     * @return mixed[]
     */
    public static function mergeRecursiveDistinct(array &$array1, array &$array2): array
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value) {
            if (\is_array($value) && isset($merged[$key]) && \is_array($merged[$key])) {
                $merged[$key] = self::mergeRecursiveDistinct($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }
}
