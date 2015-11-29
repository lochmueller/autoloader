<?php
/**
 * $EM_CONF
 *
 * @category Extension
 * @package  AutoloaderContentobject
 * @author   Tim Lochmüller
 */

/** @var $_EXTKEY string */
$EM_CONF[$_EXTKEY] = [
    'title'       => 'Autoloader (Contentobject - You should create a Teaser Content Element)',
    'description' => '',
    'constraints' => [
        'depends' => [
            'autoloader' => '1.11.1-9.9.9',
        ],
    ],
];