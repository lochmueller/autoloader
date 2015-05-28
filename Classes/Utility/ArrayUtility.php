<?php
/**
 * Arrays utility
 *
 * @category Extension
 * @package  Autoloader\Utility
 * @author   Tim Lochmüller
 */


namespace HDNET\Autoloader\Utility;

/**
 * Arrays utility
 *
 * @author Tim Lochmüller
 */
class ArrayUtility {

	/**
	 * Set a node in the array
	 *
	 * @param array $data
	 * @param array $array
	 *
	 * @see http://www.php.net/manual/de/function.array-walk-recursive.php#106340
	 */
	public static function setNodes(array $data, array &$array) {
		$separator = '|';
		foreach ($data as $name => $value) {
			if (strpos($name, $separator) === FALSE) {
				$array[$name] = $value;
			} else {
				$keys = explode($separator, $name);
				$optTree = &$array;
				while (($key = array_shift($keys))) {
					if ($keys) {
						if (!isset($optTree[$key]) || !is_array($optTree[$key])) {
							$optTree[$key] = array();
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
	 * Merge the Array Smart
	 *
	 * @param array $array1
	 * @param array $array2
	 *
	 * @return array
	 */
	public static function mergeRecursiveDistinct(array &$array1, array &$array2) {
		$merged = $array1;

		foreach ($array2 as $key => &$value) {
			if (is_array($value) && isset ($merged[$key]) && is_array($merged[$key])) {
				$merged[$key] = self::mergeRecursiveDistinct($merged[$key], $value);
			} else {
				$merged[$key] = $value;
			}
		}

		return $merged;
	}

}