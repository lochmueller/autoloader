.. index:: ! Static TypoScript

.. _static-typoscript:

Static TypoScript
^^^^^^^^^^^^^^^^^

These loader loads all setup and/or constants text files, that are in the "Configuration/TypoScript" folder structure and add them as static TypoScript include to TYPO3. The search for this text files is recursively. If there are same files in a deeper folder structure, the folder name will also added to the registration to differ between the different TypoScripts.

.. note::
	Take care that you use this call `$GLOBALS['TCA']['sys_template'] = ModelUtility::getTcaOverrideInformation('EXT_KEY_HERE', 'sys_template');` in your Configuration/TCA/Overrides/sys_template.php file.
