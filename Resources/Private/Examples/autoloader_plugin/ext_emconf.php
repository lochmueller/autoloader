<?php
/**
 * $EM_CONF
 *
 * @category Extension
 * @package  AutoloaderPlugin
 * @author   Tim Lochmüller
 */

/** @var $_EXTKEY string */
$EM_CONF[$_EXTKEY] = [
    'title'       => 'Autoloader (Plugin - There are two plugins in the plugin selection - no output)',
    'description' => '',
    'constraints' => [
        'depends' => [
            'autoloader' => '1.8.0-9.9.9',
        ],
    ],
];