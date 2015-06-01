<?php
/**
 * $EM_CONF
 *
 * @category Extension
 * @package  AutoloaderTypeconverter
 * @author   Tim Lochmüller
 */

/** @var $_EXTKEY string */
$EM_CONF[$_EXTKEY] = array(
	'title'       => 'Autoloader (TypeConverter - There are two dummy type converter in the TYPO3_CONF_VARS/EXTCONF/extbase/typeConverters)',
	'description' => '',
	'constraints' => array(
		'depends' => array(
			'autoloader' => '1.5.8-9.9.9',
		),
	),
);