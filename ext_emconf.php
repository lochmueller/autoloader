<?php
/**
 * $EM_CONF
 *
 * @category Extension
 * @package  Autoloader
 * @author   Tim Lochmüller
 */

/** @var $_EXTKEY string */
$EM_CONF[$_EXTKEY] = array(
    'title'            => 'Autoloader',
    'description'      => 'Automatic components loading of ExtBase extensions to get more time for coffee in the company ;) This ext is not a PHP SPL autoloader or class loader - it is better! Loads CommandController, Xclass, Hooks, Aspects, FlexForms, Slots, TypoScript, TypeConverter, BackendLayouts and take care of createing needed templates, TCA configuration or translations at the right location.',
    'version'          => '1.11.3',
    'state'            => 'stable',
    'clearcacheonload' => 1,
    'author'           => 'Tim Lochmüller',
    'author_email'     => 'tim.lochmueller@hdnet.de',
    'author_company'   => 'hdnet.de',
    'constraints'      => array(
        'depends' => array(
            'php'   => '5.4.0-0.0.0',
            'typo3' => '6.2.0-7.99.99',
        ),
    ),
);
