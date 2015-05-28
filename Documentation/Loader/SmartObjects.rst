.. index:: ! SmartObjects

.. _smart-objects:

SmartObjects
^^^^^^^^^^^^

"SmartObjects" are the base for the content objects and help the developer to speed up the development, if there are persistent models. All smart objects have a "@db" annotation on the given class.
If the model is mapped to a existing table, you have to add the table name like "@db tt_content". In this case, the create table statement is
create without the TYPO3 default fields like e.g. tstamp, crdate, sorting.

Furthermore the properties should have also a "@db" annotation, if the property is stored in the database. The field definition determined by the variable type of the property.
If the variable contains a complex data type, you have to add a proper field definition like "@db int(11) NOT NULL" on the right field.

.. note::
	You do not need any SQL in your ext_tables.sql file, because the smart object management register all smart objects via a slot at the database. But you can still use the ext_tables.sql if you have any kind of special tables like mm-relation tables.

.. note::
	Please also use the TcaFiles loader to create the right base TCA files for the first content objects.