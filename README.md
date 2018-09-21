# EXT:autoloader

[![Build Status](https://travis-ci.org/lochmueller/autoloader.svg?branch=master)](https://travis-ci.org/lochmueller/autoloader)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/lochmueller/autoloader/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/lochmueller/autoloader/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/lochmueller/autoloader/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/lochmueller/autoloader/?branch=master)
[![Average time to resolve an issue](http://isitmaintained.com/badge/resolution/lochmueller/autoloader.svg)](http://isitmaintained.com/project/lochmueller/autoloader "Average time to resolve an issue")
[![Percentage of issues still open](http://isitmaintained.com/badge/open/lochmueller/autoloader.svg)](http://isitmaintained.com/project/lochmueller/autoloader "Percentage of issues still open")

Autoloader: Swiss Knife for Developers
======================================

> Autoloader speeds up your development cycle - more time for coffee!

Working Examples
------
We drop the examples in EXT:autoloader.
Please check other extensions tht use autoloader as example (EXT:calendarize)

Example for a SmartObject (Only one of the features)
------

`ext_tables.php`
```php
\HDNET\Autoloader\Loader::extTables(
    'vendorName',
    'extensionKey',
    [
    	'SmartObjects',
    	'TcaFiles'
    ]
);
```

`ext_localconf.php`
```php
\HDNET\Autoloader\Loader::extLocalconf(
	'vendorName',
	'extensionKey'
	[
		'SmartObjects',
		'TcaFiles'
	]
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


Documentation
-------------
* [Online Documentation](http://docs.typo3.org/typo3cms/extensions/autoloader/)
