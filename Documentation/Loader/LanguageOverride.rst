.. index:: ! LanguageOverride

.. _languageoverride:

Language Override
^^^^^^^^^^^^^^^^^

Use this loader to create "locallangXMLOverride" configurations for foreign language files. Use the "Resources/Private/Language/Overrides" folder to create the same structure of folder like in the original extension. The first level is the extension key.

e.g. You want to override "EXT:foo_bar/mod1/locallang_mod.xlf" just create this file "Resources/Private/Language/Overrides/FooBar/mod1/locallang_mod.xlf"

The xlf files support language prefix like "de.locallang_mod.xlf".

.. code-block:: php

	$TYPO3_CONF_VARS['SYS']['locallangXMLOverride']['default']['EXT:foo/Resources/Private/Language/locallang.xlf'][] = 'EXT:bar/Resources/Private/Language/Path/To/Foo/locallang.xlf';
	$TYPO3_CONF_VARS['SYS']['locallangXMLOverride']['de']['EXT:foo/Resources/Private/Language/locallang.xlf'][] = 'EXT:bar/Resources/Private/Language/Path/To/Foo/de.locallang.xlf';