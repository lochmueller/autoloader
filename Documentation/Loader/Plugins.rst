.. index:: ! Plugins

.. _plugins:

Plugins
^^^^^^^

The "Plugins" Loader check all controller and search for actions with the "@plugin" annotation. The plugin annotation register plugins for the given controller in the ext_localconf and ext_tables process. It is possible to annotate actions in different controllers to build one plugin. But please take care, which action is the default action. If the specific action is a non cachable method, just add the "@noCache" to register this action as noCache action.

The plugin labels are translated via the extension localization mechanism.