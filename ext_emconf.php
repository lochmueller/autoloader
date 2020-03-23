<?php
/**
 * $EM_CONF
 *
 * @category Extension
 * @author   Tim Lochmüller
 */

/** @var $_EXTKEY string */
$EM_CONF[$_EXTKEY] = [
    'title'            => 'Autoloader',
    'description'      => 'Automatic components loading of ExtBase extensions to get more time for coffee in the company ;) This ext is not a PHP SPL autoloader or class loader - it is better! Loads CommandController, Xclass, Hooks, FlexForms, Slots, TypoScript, TypeConverter, BackendLayouts and take care of createing needed templates, TCA configuration or translations at the right location.',
    'version'          => '7.0.0-dev',
    'state'            => 'stable',
    'clearcacheonload' => 1,
    'author'           => 'Tim Lochmüller',
    'author_email'     => 'tim.lochmueller@hdnet.de',
    'author_company'   => 'hdnet.de',
    'constraints'      => [
        'depends' => [
            'php'   => '7.2.0-0.0.0',
            'typo3' => '10.0.0-10.99.99',
        ],
    ],
];
