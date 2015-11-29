.. index:: ! LanguageOverride

.. _languageoverride:

Language Override
^^^^^^^^^^^^^^^^^

Use this loader to create "locallangXMLOverride" configurations for language files. Recreate the same folder structure of the extension whose language files you want to override within :code:`Resources/Private/Language/Overrides`. The first level is the extension name (:code:`UpperCamelCase`). For XLIFF files language prefixes like :code:`de.locallang_mod.xlf` are supported to register overrides for translations.

For example to override :code:`EXT:foo_bar/mod1/locallang_mod.xlf` just create the file :code:`Resources/Private/Language/Overrides/FooBar/mod1/locallang_mod.xlf` and fill it with language entries. To override :code:`EXT:foo_bar/mod1/de.locallang_mod.xlf` create :code:`Resources/Private/Language/Overrides/FooBar/mod1/de.locallang_mod.xlf`.

Even if an extension follows the usual :code:`Resources/Private/Language` structure, you still have to recreate that structure within :code:`Resources/Private/Language/Overrides`, e.g. :code:`Resources/Private/Language/Overrides/BazQux/Resources/Private/Language/locallang.xlf`.

Internally the following kind of configuration will be generated:

.. code-block:: php

	$TYPO3_CONF_VARS['SYS']['locallangXMLOverride']['default']['EXT:baz_qux/Resources/Private/Language/locallang.xlf'][] = 'EXT:my_ext/Resources/Private/Language/Overrides/BazQux/Resources/Private/Language/locallang.xlf';
	$TYPO3_CONF_VARS['SYS']['locallangXMLOverride']['de']['EXT:baz_qux/Resources/Private/Language/locallang.xlf'][] = 'EXT:my_ext/Resources/Private/Language/Overrides/BazQux/Resources/Private/Language/de.locallang.xlf';
