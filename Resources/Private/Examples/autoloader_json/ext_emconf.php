<?php
/**
 * $EM_CONF
 *
 * @author   Tim Lochmüller
 * @author   Tito Duarte
 */

/** @var $_EXTKEY string */
$EM_CONF[$_EXTKEY] = [
    'title'       => 'Autoloader (Json - Try the SMD with ###YOUR-SERVER###/?eID=JsonServer&server=testService&smd=1)',
    'description' => '',
    'constraints' => [
        'depends' => [
            'autoloader' => '1.11.4-9.9.9',
        ],
    ],
];
