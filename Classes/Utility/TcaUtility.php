<?php

/**
 * TcaUtility.php.
 *
 * General file information
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Utility;

use HDNET\Autoloader\Exception;

/**
 * A basic TCA manipulation utility class.
 */
class TcaUtility
{
    /**
     * Inserts a divider tab before a given column name.
     *
     * @param array  $base       The base TCA array
     * @param string $columnName The column name before to insert a tab divider
     * @param string $tabTitle   The title of the tab
     *
     * @throws Exception
     *
     * @return array
     */
    public static function insertTabDividerBefore(&$base, $columnName, $tabTitle)
    {
        if (!\is_array($base)) {
            throw new Exception('A proper TCA configuration is needed!', 17823492);
        }

        $divider = '--div--;' . $tabTitle . ',';
        foreach ($base['types'] as $key => $layout) {
            $tempShowitem = \explode($columnName, $layout['showitem']);
            $showItem = $tempShowitem[0] . $divider . $columnName . $tempShowitem[1];
            $base['types'][$key]['showitem'] = $showItem;
        }

        return $base;
    }
}
