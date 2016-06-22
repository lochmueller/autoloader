<?php
/**
 * TcaUtility.php
 *
 * General file information
 *
 * @category     Extension
 * @package      HDNET\Autoloader\Utility
 * @subpackage   TcaUtility.php
 * @author       Christian Lewin HDNET GmbH & Co. <christian.lewin@hdnet.de>
 *
 */


namespace HDNET\Autoloader\Utility;

use HDNET\Autoloader\Exception;

/**
 * A basic TCA manipulation utility class
 *
 * @package      HDNET\Autoloader\Utility
 * @author       Christian Lewin HDNET GmbH & Co. <christian.lewin@hdnet.de>
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
     * @return array
     * @throws \Exception
     */
    public static function insertTabDividerBefore(&$base, $columnName, $tabTitle)
    {
        if (!is_array($base)) {
            throw new \Exception('A proper TCA configuration is needed!');
        }

        $divider = '--div--;' . $tabTitle . ',';
        foreach ($base['types'] as $key => $layout) {
            $tempShowitem = explode($columnName, $layout['showitem']);
            $showItem = $tempShowitem[0] . $divider . $columnName . $tempShowitem[1];
            $base['types'][$key]['showitem'] = $showItem;
        }
        return $base;
    }
}
