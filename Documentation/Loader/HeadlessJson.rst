.. index:: ! HeadlessJson

.. _headless-json:

Headless Json
^^^^^^^^^^^^^^^

Headless Json is a sub type of content objects (the loader register the headless json as smart objects, to get all smart
object functions) and generates the typoscript needed to serialize the content object into json into this directory:
"Resources/Private/TypoScript/Content/ContentObjec.typoscript".

If the @noHeader annotation is present on the content object, no header will be included in the output.
If an object or an object storage of an object is specified in the @databaseField annotation, it will be serialized with
all of its properties. This is done recursively for all relations. The loader (in case of an object storage) identifies
the foreign key from the domain model, to which the object storage points (property with type equal to the current type).
Many to many relations are not correctly selected as of now.
