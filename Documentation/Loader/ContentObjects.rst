.. index:: ! Content Objects

.. _content-objects:

Content Objects
^^^^^^^^^^^^^^^

Content objects are a sub type of smart objects (the loader register the content object as smart objects, to get all smart object functions) and placed in this folder: "Classes/Domain/Model/Content/". Content objects always extends the tt_content table and need a "@db tt_content" annotation (see smart objects). You can use existing fields on the one hand and create other custom fields on the other hand. Each content object is registered as CType in the TYPO3 content element handling. There is one central Controller that render the CTypes and take care, that the right domain model is selected and the right template ist rendered. The template location is "Resources/Private/Templates/Content/" and the template has the same name as the domain model (also upper camel case). There are two possibilities to get access to the data. You can access "data" and "object" in fluid. Data is the whole database row and object is the domain model with the mapped properties.

If you do not want to use the css_styled_content default header, please add the @noHeader annotation to the content object.

Summarize: Create a domain model, create a template, maybe run database compare, create and use the new content element.

.. note::
	These are real content elements and no flex form elements like in many other extensions.

.. note::
	Please also use the TcaFiles loader to create the right base TCA files for the first content objects.

.. note::
	The Autoloader creates two Template files for the ContentObject.
	- the normal template-file for the FE
	- a backend preview template (with suffix Backend)

	If you don't need a Backend Preview for your ContentObject you can delete the template file and it will not be created again. The old behavior is in place.