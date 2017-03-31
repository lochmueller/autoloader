<?php
/**
 * $EM_CONF
 *
 * @category Extension
 * @package  AutoloaderCsh
 * @author   Tim Lochmüller
 */

/** @var $_EXTKEY string */
$EM_CONF[$_EXTKEY] = [
    'title'       => 'Autoloader (CSH - create context sensitive help files for the smart objects)',
    'description' => '',
    'constraints' => [
        'depends' => [
            'autoloader' => '3.0.0',
        ],
    ],
];
