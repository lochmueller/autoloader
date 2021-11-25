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
    'version' => '7.2.4',
    'state'            => 'stable',
    'clearcacheonload' => 1,
    'author'           => 'Tim Lochmüller',
    'author_email'     => 'tim.lochmueller@hdnet.de',
    'author_company'   => 'hdnet.de',
    'constraints'      => [
        'depends' => [
            'php'   => '7.3.0-8.0.99',
            'typo3' => '10.4.6-11.5.99',
        ],
    ],
];
