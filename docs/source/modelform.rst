.. _overriding_the_clean_method:

Overriding the clean() method
-----------------------------

You can override the **clean()** method on a model form to provide additional validation in the same way you can on a
normal form.

A model form instance attached to a model object will contain an **modelInstance** attribute that gives its methods access
to that specific model instance.