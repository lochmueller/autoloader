General
^^^^^^^

The autoloader has a simple API, that take care to trigger the different loaders of the autoloader extension. In the :index:`ext_localconf` and :index:`ext_tables` files of your extension, you have to trigger the static loader function. Furthermore you **have to** set autoloader in the dependencies of your extensions :index:`ext_emconf` (so the extensions are load in the right order).

To trigger the autoloader, please add the following lines to your ext_localconf.php and ext_tables.php files:

.. code-block:: php

   // in ext_localconf.php
   \HDNET\Autoloader\Loader::extLocalconf('VENDORNAME', 'extension_key');

.. code-block:: php

	// in ext_tables.php
	\HDNET\Autoloader\Loader::extTables('VENDORNAME', 'extension_key');


In the basic configuration this lines will trigger all loader of the autoloader extension. The loader are always split into three parts:

- *prepare Loader information:* The loader prepare complex information (reflection, file listings, search and replace, prepare information) and store that information into an array. The autoloader extension take care, that the array is cached, so the next calls are smart and fast.
- *ext_tables execute:* The code that is execute in the ext_tables
- *ext_localconf execute:* The code that is execute in the ext_localconf

Furthermore you have the possibility to select only a few loaders to increase the performance of the auto loading process. This is possible by adding a array as third parameter including the names of the Loader that you need.

.. note::
   Please use the same Loader for the ext_tables and ext_localconf file.

.. code-block:: php

   // Example in ext_localconf.php
   \HDNET\Autoloader\Loader::extLocalconf('VENDORNAME', 'extension_key', array('Xclass', 'Slots'));


.. code-block:: php

   // Example in ext_tables.php
   \HDNET\Autoloader\Loader::extTables('VENDORNAME', 'extension_key', array('Xclass', 'Slots'));