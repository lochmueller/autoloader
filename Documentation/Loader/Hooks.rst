.. index:: ! Hooks

.. _hooks:

Hooks
^^^^^

Hooks are located in the folder "Classes/Hooks". The loader scan the directory and check the classes via PHP reflection against certain annotations. The keyword is "@hook" and the class or method (based on the target hook) have to be marked with this keyword. If the keyword ist part of the class PHPDoc, the hook register the class name and if the annotation is part of a method PHPDoc the autoloader will register the method (TYPO3 syntax class name -> method name) to the given hook.

To define the target of the hook (in TYPO3 normally a array path in TYPO3_CONF_VARS) you have to set the target after the @hook annotation, separated by pipes. Example:

.. code-block:: php

	/**
	 * Description
	 *
	 * @hook TYPO3_CONF_VARS|SC_OPTIONS|recordlist/mod1/index.php|drawFooterHook
	 */
	function testFunction(){
		// do something
	}