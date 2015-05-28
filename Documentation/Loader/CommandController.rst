.. index:: ! Command Controller

..  _command-controller:

Command Controller
^^^^^^^^^^^^^^^^^^

The "CommandController" autoloader register all command controller for the new handling of the :index:`scheduler` in the extbase-way. The loader expect the command controller classes in the ``Classes/Command/`` folder of the given extension. All classes will be analyzed and finally registered to the :index:`TYPO3_CONF_VARS`.