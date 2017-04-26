Bound and unbound forms
#######################

A Form instance is either bound to a set of data, or unbound.

If it's bound to a set of data, it's capable of validating that data and rendering the form as HTML with the data
displayed in the HTML.

If it's unbound, it cannot do validation (because there's no data to validate!), but it can still render the blank form
as HTML.

class Form
----------

To create an unbound Form instance, simply instantiate the class:

.. code-block:: php

    $form = new AuthorForm();

To bind data to a form, pass the data as a dictionary as the first parameter to your Form class constructor:

.. code-block:: php

    $data =[
        "name" => "rrrr"
        "email" => "edd.cowan@gmail.com"
    ];
    $form = new AuthorForm($data);

In this associative array, the keys are the field names, which correspond to the attributes in your Form class. 
The values are the data you're trying to validate. These will usually be strings, but there's no requirement that they
be strings; the type of data you pass depends on the Field, as we'll see in a moment.

Form.isBound
------------

If you need to distinguish between bound and unbound form instances at runtime, check the value of the form's is_bound
attribute:

.. code-block:: php

    $form = new AuthorForm();
    var_dump($form->isBound); // false

    $form = new AuthorForm($data);
    var_dump($form->isBound); // true

Note that passing an empty dictionary creates a bound form with empty data:

.. code-block:: php

    $form = new AuthorForm([]);
    var_dump($form->isBound); // true

If you have a bound Form instance and want to change the data somehow, or if you want to bind an unbound Form instance
to some data, create another Form instance. There is no way to change data in a Form instance.

Once a Form instance has been created, you should consider its data immutable, whether it has data or not.

Using forms to validate data
----------------------------

.. _form_clean:

Form.clean()
............

Implement a clean() method on your Form when you must add custom validation for fields that are interdependent.
See :ref:`Cleaning and validating fields that depend on each other <validating_fields_with_clean>` for example usage.

Form.isValid()
..............

The primary task of a Form object is to validate data. With a bound Form instance, call the is_valid() method to run
validation and return a boolean designating whether the data was valid:


.. code-block:: php

    $data =[
        "name" => "rrrr"
        "email" => "edd.cowan@gmail.com"
    ];
    $form = new AuthorForm($data);
    var_dump($form->isValid()); // true

Let's try with some invalid data. In this case, subject is blank (an error, because all fields are required by default)
and sender is not a valid email address:

.. code-block:: php

    $data =[
        "name" => "rrrr"
        "email" => "edd.gmail.com"
    ];
    $form = new AuthorForm($data);
    var_dump($form->isValid()); // false

.. _form_errors:

Form.errors()
.............

Access the errors method to get a dictionary of error messages:

.. code-block:: php

    var_dump($form->errors());

    array:2 [▼
      "name" => array:1 [▼
        0 => ValidationError {#92 ▶}
      ]
      "email" => array:1 [▼
        0 => ValidationError {#93 ▶}
      ]
    ]

Returns an associative array of fields to their original ValidationError instances.

.. _form_add_error:

Form.addError($field, $error)
.............................

This method allows adding errors to specific fields from within the **Form.clean()** method, or from outside the form
altogether; for instance from a view.

The **field** argument is the name of the field to which the errors should be added. If its value is None the error
will be treated as a non-field error as returned by :ref:`Form.nonFieldErrors() <non_field_errors>`.

The error argument can be a simple string, or preferably an instance of ValidationError. See
:ref:`Raising ValidationError<raising_validation_error>` for best practices when defining form errors.

Note that **Form.addError()** automatically removes the relevant field from **cleaned_data**.

.. _form_has_error:

Form.hasError($field, $code=null)
.................................

This method returns a boolean designating whether a field has an error with a specific error **code**.
If **code** is **null**, it will return **true** if the field contains any errors at all.

To check for non-field errors use :ref:`NON_FIELD_ERRORS<non_field_errors>` as the field parameter.

.. _non_field_errors:

Form.nonFieldErrors()
.....................

This method returns the list of errors from :ref:`Form.errors()<form_errors>` that aren't associated with a
particular field. This includes ValidationErrors that are raised in :ref:`Form.clean()<form_clean>` and errors added
using :ref:`Form.addError(null, "...")<form_add_error>`.