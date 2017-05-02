<?php

namespace Eddmash\PowerOrm\Form;

use Eddmash\PowerOrm\BaseObject;
use Eddmash\PowerOrm\ContributorInterface;
use Eddmash\PowerOrm\Exception\FieldDoesNotExist;
use Eddmash\PowerOrm\Exception\FormNotReadyException;
use Eddmash\PowerOrm\Exception\ImproperlyConfigured;
use Eddmash\PowerOrm\Exception\KeyError;
use Eddmash\PowerOrm\Exception\ValidationError;
use Eddmash\PowerOrm\Form\Fields\Field;
use Eddmash\PowerOrm\Form\Helpers\ErrorDict;
use Eddmash\PowerOrm\Form\Helpers\ErrorList;
use Eddmash\PowerOrm\Helpers\ArrayHelper;
use Eddmash\PowerOrm\Helpers\Tools;

/**
 * Class Form.
 *
 * @since 1.0.0
 *
 * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
 */
abstract class Form extends BaseObject implements \IteratorAggregate
{
    use FormFieldTrait;
    const nonFieldErrors = '_all_';

    /**
     * @var ErrorDict
     */
    private $errors;

    /**
     * Indicates if the form is ready for use, if false, this indicates the form is in customization mode and cannot
     * be used for things like validation.using it when not ready causes inconsistencies in how the form works.
     *
     * Call done() to signal your done customizing the form
     *
     * @var bool
     */
    protected $ready = false;

    /**
     * By default, the form rendering methods include:
     *      - HTML id attributes on the form elements.
     *      - The corresponding <label> tags around the labels. An HTML <label> tag designates which label text is
     *          associated with which form element.
     *          This small enhancement makes forms more usable and more accessible to assistive devices.
     *          It’s always a good idea to use <label> tags.
     *
     * The id attribute values are generated by prepending id_ to the form field names. This behavior is configurable,
     * though, if you want to change the id convention or remove HTML id attributes and <label> tags entirely.
     *
     * Use the auto_id argument to the Form constructor to control the id and label behavior.
     *
     * This argument must be True, False or a string.
     *      - If auto_id is False, then the form output will not include <label> tags nor id attributes
     *      - If auto_id is set to True, then the form output will include <label> tags and will simply use the
     *          field name as its id for each form field.
     *      - If auto_id is set to a string containing the format character '%s', then the form output will include
     *          <label> tags, and will generate id attributes based on the format string. For example,
     *          for a format string 'field_%s', a field named subject will get the id value 'field_subject'.
     *      - If auto_id is set to any other true value – such as a string that doesn’t include %s – then the library
     *          will act as if auto_id is True.
     *
     * By default, auto_id is set to the string 'id_%s'.
     *
     * @var string
     */
    public $autoId = 'id_%s';

    public $initial = [];
    /**
     * @var array mostly from
     */
    public $data = [];

    /**
     * @var array mostly from
     */
    public $files = [];

    public $prefix;
    public $labelSuffix;

    public $isBound = false;

    /**
     * @var Field[]
     */
    protected $fieldsCache = [];
    public $validation_rules = [];
    public $cleanedData = [];

    /**
     * Takes three arguments.
     *
     * @param array $data    the data to bind the form to and validate against, usually you will use data from the $_POST
     *                       but can be an associative array that has any of the form fields names as keys
     * @param array $initial this is the are initial values for the form fields usually the first time the form is
     *                       loaded i.e. unbound form, this should be an associative array where keys are the form fields names
     *
     * You may be thinking, why not just pass a dictionary of the initial values as data when displaying the form?
     * Well, if you do that, you’ll trigger validation, and the HTML output will include any validation errors.
     *
     * This is why initial values are only displayed for unbound forms. For bound forms, the HTML output will use
     * the bound data.
     *
     * Also note that initial values are not used as “fallback” data in validation if a particular field’s value is
     * not given. initial values are only intended for initial form display:
     *
     * <strong>NOTE </strong> this are not default values
     * @param array $kwargs this accepts any other arguments that need to be passed to the form, usually
     *                      this used to accept user defined arguments
     */
    public function __construct($kwargs = [])
    {
        $data = ArrayHelper::pop($kwargs, 'data', []);
        $initial = ArrayHelper::pop($kwargs, 'initial', []);
        if (!empty($data)):
            $this->isBound = true;
        endif;

        $this->data = $data;

        $this->initial = $initial;

        // replace the default options with the ones passed in.

        if ($kwargs) :
            foreach ($kwargs as $key => $value) :
                $this->{$key} = $value;
            endforeach;
        endif;

        if(is_null($this->labelSuffix)):
            $this->labelSuffix = ':';
        endif;

        $this->errors = ErrorDict::instance();

        $this->setup();

    }

    /**
     * Initializes the object.
     * This method is invoked at the end of the constructor after the object is initialized ;.
     */
    public function init()
    {
    }

    /**
     * Returns an array of fields to be attached to the form.
     *
     * @return Field[]
     * @author: Eddilbert Macharia (http://eddmash.com)<edd.cowan@gmail.com>
     */
    public function fields()
    {
        return [];

    }

    public function setup()
    {
        if (!$this->ready):
            $fields = $this->fields();
            // this sets the form fields to the form.
            foreach ($fields as $name => $field) :
                $this->addField($name, $field);
            endforeach;

            $this->ready = true;
        endif;
    }

    /**
     * Returns true if the form is bound and its has no errors after validation has been run.
     *
     * @return bool
     *
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public function isValid()
    {
        $this->_isReady(__METHOD__);

        return $this->isBound && $this->formHasErrors();
    }

    /**
     * return a list of errors related to the form and its fields.
     *
     * @return ErrorDict
     *
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public function errors()
    {

        if ($this->errors->isEmpty()):
            $this->fullClean();
        endif;

        return $this->errors;
    }

    /**
     * Raise error if form is not ready for use.
     *
     * @param $method
     *
     * @throws FormNotReadyException
     *
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    protected function _isReady($method)
    {
        if (!$this->ready):
            $this->setup();
        endif;
    }

    /**
     * Returns true of form is ready for use or false if its still in customizaiton mode.
     *
     * @return bool
     *
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public function isReady()
    {
        return $this->ready;
    }

    /**
     * Clean the form and the fields i.e. do the validations for this form and its fields.
     *
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public function fullClean()
    {
        if (!$this->isBound):
            return;
        endif;

        $this->cleanFields();
        $this->cleanForm();
        $this->postClean();
    }

    /**
     * An internal hook on which to run extra validation work after the form is done with it validation.
     *
     * used by ModelForm.
     *
     * @author: Eddilbert Macharia (http://eddmash.com)<edd.cowan@gmail.com>
     */
    public function postClean()
    {

    }

    /**
     * Gets a single field instance in the form fields array and returns it.
     *
     * <h4>Usage</h4>
     *
     * if a form has a fields username, you get the field object:
     *
     * <pre><code>$form->getField('username);</code></pre>
     *
     * @param $field_name
     *
     * @return mixed
     *
     * @throws FieldDoesNotExist
     *
     * @since 1.0.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public function getField($field_name)
    {
        if (ArrayHelper::hasKey($this->fieldsCache, strtolower($field_name))):
            return ArrayHelper::getValue($this->fieldsCache, strtolower($field_name));
        endif;
        throw new FieldDoesNotExist(sprintf('Field "%s" not found in "%s" ', $field_name, static::class));
    }

    /**
     * Returns all the field on the current form.
     *
     * @return Field[]
     * @author: Eddilbert Macharia (http://eddmash.com)<edd.cowan@gmail.com>
     */
    public function getFields()
    {
        $this->setup();

        return $this->fieldsCache;
    }

    /**
     * used to set up field on the form, usually used by a fields contribute method.
     *
     * @param $field
     *
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public function loadField($field)
    {
        $this->fieldsCache[strtolower($field->name)] = $field;
    }

    public function clean()
    {
        return $this->cleanedData;
    }

    public function addError($name, $error)
    {

        // for consistency convert them to a validation error object
        if (!$error instanceof ValidationError):
            $error = new ValidationError($error);
        endif;

        if (!$name):
            $name = self::nonFieldErrors;
        endif;

        $this->errors[$name] = $error->getErrorList();

        if (ArrayHelper::hasKey($this->cleanedData, $name)) :
            unset($this->cleanedData[$name]);
        endif;

    }

    public function addField($name, $field)
    {
        $this->fieldSetup($name, $field);
    }

    public function nonFieldErrors()
    {
        if (ArrayHelper::hasKey($this->errors(), self::nonFieldErrors)):
            return ArrayHelper::getValue($this->errors(), self::nonFieldErrors);
        endif;

        return ErrorList::instance([], 'nonfield');
    }

    /**
     * Returns only hidden fields.
     *
     * @return Field[]
     *
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public function hiddenFields()
    {
        $hiddenFields = [];
        foreach ($this->fieldsCache as $name => $field) :
            if ($field->isHidden()):
                $hiddenFields[$name] = $field;
            endif;
        endforeach;

        return $hiddenFields;
    }

    /**
     * REturns only non-hidden fields.
     *
     * @return Field[]
     *
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public function visibleFields()
    {
        $visibleFields = [];
        foreach ($this->fieldsCache as $name => $field) :
            if (!$field->isHidden()):
                $visibleFields[$name] = $field;
            endif;
        endforeach;

        return $visibleFields;
    }

    /**
     * Returns this form rendered as HTML <li>s -- excluding the <ul></ul>.
     *
     * @return string
     *
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public function asUl()
    {
        return $this->getHtmlOutput(
            [
                'row' => '<li> %s %s %s</li>',
                'errors' => '<li>%s</li>',
                'helpText' => '<span class="helptext">%s</span>',
            ]
        );
    }
    /**
     * Returns this form rendered as HTML <li>s -- excluding the <ul></ul>.
     *
     * @return string
     *
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public function asTable()
    {
        return $this->getHtmlOutput(
            [
                'row' => '<tr><th>%s</th><td>%s%s</td></tr>',
                'errors' => '<tr><td colspan="2">%s</td></tr>',
                'helpText' => '<br><span class="helptext">%s</span>',
            ]
        );
    }

    /**
     * Returns this form rendered as HTML <p>s.
     *
     * @return string
     *
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public function asParagraph()
    {
        return $this->getHtmlOutput(
            [
                'row' => '<p> %s <br> %s <br> %s</p>',
                'errors' => '%s',
                'helpText' => '<span class="helptext">%s</span>',
            ]
        );
    }

    /**
     * Gets the initial value for a field.
     *
     * @param Field $field
     * @param $fieldName
     * @author: Eddilbert Macharia (http://eddmash.com)<edd.cowan@gmail.com>
     *
     * @return array|mixed|null
     */
    public function getInitialForField(Field $field, $fieldName)
    {
        if (ArrayHelper::hasKey($this->initial, $fieldName)):
            $initial = ArrayHelper::getValue($this->initial, $fieldName);
        else:
            $initial = $field->initial;
        endif;

        if (is_callable($initial)) :
            $initial = call_user_func($initial);
        endif;

        return $initial;
    }

    /**
     * Clean form fields.
     *
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    private function cleanFields()
    {
        foreach ($this->fieldsCache as $name => $field) :
            // if field has failed validation, no need to go on
            if (ArrayHelper::hasKey($this->errors, $name)):
                continue;
            endif;

            if ($field->disabled):
                $value = $this->getInitialForField($field, $name);
            else:
                if (ArrayHelper::hasKey($this->data, $name)):

                    $value = $field->widget->valueFromDataCollection($this->data, $this->files, $name);
                else:
                    $value = $field->data();
                endif;
            endif;

            try {

                // run default field validations
                $value = $field->clean($value);
                // just in case, confirm the field has not field validation already
                if (!ArrayHelper::hasKey($this->errors, $name)):
                    $this->cleanedData[$name] = $value;
                endif;

                // run custom validation by user
                $fieldCleanMethod = sprintf('clean%s', ucfirst($name));
                if ($this->hasMethod($fieldCleanMethod)):
                    $value = call_user_func([$this, $fieldCleanMethod]);
                    $this->cleanedData[$name] = $value;
                endif;
            } catch (ValidationError $e) {

                $this->addError($name, $e);

                if (ArrayHelper::hasKey($this->cleanedData, $name)):
                    unset($this->cleanedData[$name]);
                endif;
            }

        endforeach;
    }

    private function cleanForm()
    {
        try {
            $cleanData = $this->clean();
        } catch (ValidationError $e) {
            $cleanData = null;
            $this->addError(null, $e);
        }

        if ($cleanData):
            $this->cleanedData = $cleanData;
        endif;
    }

    private function formHasErrors()
    {
        return empty($this->errors());
    }

    /**
     * Returns True if the form needs to be multipart-encoded, i.e. it has FileInput. Otherwise, False.
     *
     * @return bool
     *
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public function isMultipart()
    {
        if (empty($this->fieldsCache)):
            return false;
        endif;

        foreach ($this->fieldsCache as $field) :
            if ($field->widget->needsMultipartForm):
                return true;
            endif;
        endforeach;

        return false;
    }

    /**
     * @return string
     *
     * @since 1.0.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    protected function getHtmlOutput($opts = [])
    {
        //todo display errros
        /* @var $field Field */
        $topErrors = $this->nonFieldErrors();

        $row = '';
        $errors = '';
        $helpText = '%s';
        extract($opts);

        $output = [];
        $hidden_output = [];

        foreach ($this->fieldsCache as $name => $field) :
            $fieldErrors = null;

            if ($field->getErrors()) :
                $errs = [];
                foreach ($field->getErrors() as $error) :
                    $errs[] = (string) $error;
                endforeach;
                $fieldErrors = ErrorList::instance($errs);
            endif;

            if ($field->isHidden()):
                if ($field->getErrors()) :
                    $errs = [];
                    foreach ($field->getErrors() as $fieldError) :
                        $errs[] = sprintf('(Hidden field :: %s ) %s', $name, $fieldError);
                    endforeach;

                    $topErrors->extend($errs);
                endif;
                $hidden_output[] = $field->asWidget();
            else:
                if (!$fieldErrors->isEmpty()) :
                    $output[] = sprintf($errors, $fieldErrors);
                endif;

                $helpTextHtml = '';
                if($field->getHelpText()):
                    $helpTextHtml = sprintf($helpText, $field->getHelpText());
                endif;

                $output[] = sprintf($row, $field->labelTag(), $field->asWidget(), $helpTextHtml);
            endif;
        endforeach;

        // add errors to the top
        if (!$topErrors->isEmpty()) :
            array_unshift($output, sprintf($errors, $topErrors));
        endif;

        // add hidden inputs to end
        $output = array_merge($output, $hidden_output);

        return implode(' ', $output);
    }

    protected function fieldSetup($name, $value)
    {
        if ($value instanceof ContributorInterface):
            $value->contributeToClass($name, $this);
        else:
            $this->{$name} = $value;
        endif;
    }

    /**
     * @return Field[]
     *
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public function getIterator()
    {
        $this->setup();

        return new \ArrayIterator($this->fieldsCache);
    }

    /**
     * @ignore
     *
     * @param $field_name
     *
     * @return mixed
     *
     * @throws KeyError
     *
     * @since 1.0.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public function __get($field_name)
    {
        $this->setup();

        if (ArrayHelper::hasKey($this->fieldsCache, $field_name)):
            return $this->getField($field_name);
        endif;
    }

    public function __set($name, $value)
    {
        $this->fieldSetup($name, $value);
    }

    public function __toString()
    {
        try {
            $this->setup();

            return $this->asParagraph();
        } catch (\Exception $exception) {
            Tools::convertExceptionToError($exception);

            return '';
        }
    }

    /**
     * Returns the field name with a prefix appended, if this Form has a prefix set.
     *
     * @param $name
     *
     * @return string
     */
    public function addPrefix($name)
    {
        return ($this->prefix) ? sprintf('%s-%s', $this->prefix, $name) : $name;
    }
}
