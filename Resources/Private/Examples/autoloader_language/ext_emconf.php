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
    'title'       => 'Autoloader (language - Check the TYPO3_CONF_VARS - there are new overrides)',
    'description' => '',
    'constraints' => [
        'depends' => [
            'autoloader' => '1.10.0-9.9.9',
        ],
    ],
];