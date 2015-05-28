.. index:: ! Aspect

..  _aspect:

Aspect
^^^^^^

The "Aspect" autoloader register registers aspects like before, replace, after and throw for all classes available. The aspect mechanism based on a :index:`Xclass` that extends the class to attach the aspects to the joinPoints.

.. warning::
   You can only use aspects on classes that doesn't been xclassed by **any other extension**.