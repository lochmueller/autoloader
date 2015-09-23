<?php
/**
 * $EM_CONF
 *
 * @author   Tim Lochmüller
 */

/** @var $_EXTKEY string */
$EM_CONF[$_EXTKEY] = [
    'title'       => 'Autoloader (Soap - Try the WSDL with ###YOUR-SERVER###/?eID=SoapServer&server=test&wsdl=1)',
    'description' => '',
    'constraints' => [
        'depends' => [
            'autoloader' => '1.9.1-9.9.9',
        ],
    ],
];