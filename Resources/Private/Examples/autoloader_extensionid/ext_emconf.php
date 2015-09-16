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
    'title'       => 'Autoloader (eID - run http://your-domain.de/?eID=Test to get a Hello World)',
    'description' => '',
    'constraints' => [
        'depends' => [
            'autoloader' => '1.8.0-9.9.9',
        ],
    ],
];