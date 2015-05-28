.. index:: ! Slots

.. _slots:

Slots
^^^^^

The "Slots" Loader scans the "Classes/Slots" folder, to connect the given class methods to specific signals. All class will be checked. The slot methods have to annotate with "@signalClass"
and "@signalName" to point to the target signal. So it is possible to connect one slot class with many signals.