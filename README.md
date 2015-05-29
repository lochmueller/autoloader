# autoloader
TYPO3 CMS Extension - autoloader

Autoloader: Swiss Knife for Developers
======================================

> Autoloader speeds up your development cycle - more time for coffee!

Working Examples
------
See how simple it is and how the Autoloader works.

* First: install Autoloader
* Second: execute autoloader/Resources/Private/Shell/LinkExampleExtensions.sh
```bash
cd typo3conf/ext/
bash autoloader/Resources/Private/Shell/LinkExampleExtensions.sh
```
* Third: install in your TYPO3 Extension-Manager the example extensions one-by-one.
* Fourth: explore!


Comfortable Annotations
-----------

| Loader                        | Folder                                  | Class-Tag                                      | Method-Tag                                    |
|-------------------------------|-----------------------------------------|------------------------------------------------|-----------------------------------------------|
| AlternativeImplementations    | ``Classes/AlternativeImplementations/`` |                                                |                                               |
| Aspect                        | ``Classes/Aspect/``                     |                                                | @aspectClass, @aspectJoinPoint, @aspectAdvice |
| CommandController             | ``Classes/Command/``                    |                                                |                                               |
| ContentObjects                | ``Classes/Domain/Model/Content/``       | see SmartObjects, @noHeader, @wizardTab        | see SmartObjects                              |
| ExtensionId                   | ``Resources/Private/Php/eID/``          |                                                |                                               |
| ExtensionTyposcript           | ``Classes/Domain/Model/``               | @db, @recordType, @parentClass                 |                                               |
| FlexForms                     | ``Configuration/FlexForms/``            |                                                |                                               |
| Hooks                         | ``Classes/Hooks/``                      | @hook                                          | @hook                                         |
| Plugins                       | ``Classes/Controller/``                 |                                                | @plugin, @noCache                             |
| Slots                         | ``Classes/Slots/``                      |                                                | @signalClass, @signalName                     |
| SmartObjects                  | ``Classes/Domain/Model/``               | @db, @smartExclude, @recordType, @parentClass  | @db, @enableRichText                          |
| StaticTyposcript              | ``Configuration/TypoScript/``           |                                                |                                               |
| TcaFiles                      | ``Configuration/TCA/`` + Overrides      |                                                |                                               |
| TypeConverter                 | ``Classes/Property/TypeConverter/``     |                                                |                                               |
| Xclass                        | ``Classes/Xclass/``                     |                                                |                                               |



Example for a SmartObject
------

`ext_tables.php`
```php
\HDNET\Autoloader\Loader::extTables(
    'vendorName',
    'extensionKey',
    array(
    	'SmartObjects',
    	'TcaFiles'
    )
);
```

`ext_localconf.php`
```php
\HDNET\Autoloader\Loader::extLocalconf(
	'vendorName',
	'extensionKey'
	array(
		'SmartObjects',
		'TcaFiles'
	)
);
```

`Test.php`
```php
namespace vendorName\extensionKey\Domain\Model;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
/**
 * Create a test-table for this model with this annotation.
 * @db
 */
class Test extends AbstractEntity {

	/**
	 * A basic field
	 *
	 * @var string
	 * @db
	 */
	protected $textField;

	/**
	 * A boolean field
	 *
	 * @var bool
	 * @db
	 */
	protected $boolField;

	/**
	 * File example
	 *
	 * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
	 * @db
	 */
	protected $file;

	/**
	 * Custom (variable that has a custom DB type)
	 *
	 * @var int
	 * @db int(11) DEFAULT '0' NOT NULL
	 */
	protected $customField;
	
	// add here some Getters and Setters
}
```


Highlights
----------
* 
* 

Documentation
-------------
