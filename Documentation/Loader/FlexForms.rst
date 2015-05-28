.. index:: ! FlexForms

.. _flex-forms:

FlexForms
^^^^^^^^^

FlexForms are located in the "Configuration/FlexForms/" folder. All flex forms are XML files (please check TYPO3 core documentation) with the name of the plugin (upper camel case). The loader scans the folder and register the XML files to the TYPO3 core. So: Just use the same name for your Plugin and flex form file.

If there are FlexForms in the "Configuration/FlexForms/Content/" folder, the configuration XML files are related to Autoloader content objects and will be registered to the content element. In the content element, you can access the configuration values via settings, link in extbase.

.. note::
	The loader change the subtypes_excludelist and subtypes_addlist of the given plugin signature. If you have any custom changes to this properties of tt_content you have to run your changes after the autoloader call.