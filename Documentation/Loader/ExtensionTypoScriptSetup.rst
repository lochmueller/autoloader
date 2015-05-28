.. index:: ! Extension TypoScript

.. _extension-typoscript:

Extension TypoScript
^^^^^^^^^^^^^^^^^^^^

This loader register additional TypoScript to the rendering process to describe the model in detail. All annotations are class annotations and no method annotations. Possible annotations and results are...

@db
The annotation is also used by the smart object handling. If a model is tagged with @db this loader will add the following line the the TS:
config.tx_extbase.persistence.classes.YOUR\CLASS\Name.mapping.tableName = target_table_with_db_annotation

@recordType
The annotation is used, if the current model is part of a record type set. After the tag you have to define the record type field. The annotation generate and register in the following line of TS:
config.tx_extbase.persistence.classes.YOUR\CLASS\Name.mapping.recordType = record_type_field

@parentClass
The annotation is used, if the current model is part of a subclass set. After the tag you have to define the subclass name. The annotation generate and register the following TS:
config.tx_extbase.persistence.classes.YOUR\CLASS\Name.subclasses.YOUR\Class\Subclass\Name = YOUR\CLASS\Name