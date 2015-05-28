.. index:: Aspect

Aspect Handling
^^^^^^^^^^^^^^^

The aspect handling is a mechanism to handle xclass dependencies on different classes. It is a wrapper for xclass related functions. Technically the aspact handling collect information about the extending class and how the class should extend the target.

There are three different types of annotation, that helps to extend a foreign class: @aspectClass, @aspectJoinPoint and @aspectAdvice
The class is the target class (e.g. \TYPO3\CMS\Recordlist\RecordList) and the joinPoint is the target method name.

The advice describe the structure of the aspect. There are different advice types, that call the aspect method in a different context.

before : call the method in front of the original method
replace : call the method instead of the original method
after : call the method after the original method
throw : call the method, if the target throw an exception to handle the error

Technically the aspect loader register a PHP SPL autoloader that load an generated xclass class that extend the original. The generated class take care of the different aspect advices. So it is possible, that two different extensions extend the same original class, without a direct conflict.

.. note::
	This mechanism is still experimental. We need feedback to make the feature stable.