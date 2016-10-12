<?php
/**
 * $EM_CONF
 *
 * @category Extension
 * @package  AutoloaderHooks
 * @author   Tim Lochmüller
 */

/** @var $_EXTKEY string */
$EM_CONF[$_EXTKEY] = [
    'title'       => 'Autoloader (Icon - Register same icons)',
    'description' => '',
    'constraints' => [
        'depends' => [
            'autoloader' => '2.1.0-9.9.9',
        ],
    ],
];
