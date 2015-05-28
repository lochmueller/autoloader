.. index:: ! eID
.. index:: ! ExtensionId

.. _extension-id:

ExtensionId (eID)
^^^^^^^^^^^^^^^^^

The ExtensionId loader loads the classes in from the folder "Resources/Private/Php/eID/" and register the PHP files in this folder as TYPO3 eID scripts. The identifier of the eID script is the file base name. If the script file is "Register.php" you can access the script via "/?eID=Register". Please use internally service classes to control the application.

.. note::
	The eID script is only the "mini-bootstrap" of the eID call.