<?php
/**
 * $EM_CONF
 *
 * @category Extension
 * @package  AutoloaderSlots
 * @author   Tim Lochmüller
 */

/** @var $_EXTKEY string */
$EM_CONF[$_EXTKEY] = array(
	'title'       => 'Autoloader (Slots - Check the TYPO3 Login screen)',
	'description' => '',
	'constraints' => array(
		'depends' => array(
			'autoloader' => '1.5.8-9.9.9',
		),
	),
);