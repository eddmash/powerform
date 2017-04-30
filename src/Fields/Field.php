<?php
/**
 * Created by eddmash <http://eddmash.com>
 * Date: 6/23/16
 * Time: 3:55 PM.
 */

namespace Eddmash\PowerOrm\Form\Fields;

use Eddmash\PowerOrm\BaseObject;
use Eddmash\PowerOrm\ContributorInterface;
use Eddmash\PowerOrm\Exception\ValueError;
use Eddmash\PowerOrm\Exception\ValidationError;
use Eddmash\PowerOrm\Form\Form;
use Eddmash\PowerOrm\Form\Helpers\ErrorList;
use Eddmash\PowerOrm\Form\Widgets\TextInput;
use Eddmash\PowerOrm\Form\Widgets\Widget;
use Eddmash\PowerOrm\Helpers\ArrayHelper;
use Eddmash\PowerOrm\Helpers\Tools;

/**
 * Base class for all form fields, should nevers be initialized, use its subclasses.
 *
 * required -- Boolean that specifies whether the field is required.
 *             True by default.
 * widget -- A Widget class, or instance of a Widget class, that should
 *           be used for this Field when displaying it. Each Field has a
 *           default Widget that it'll use if you don't specify this. In
 *           most cases, the default widget is TextInput.
 * label -- A verbose name for this field, for use in displaying this
 *          field in a form. By default, Django will use a "pretty"
 *          version of the form field name, if the Field is part of a
 *          Form.
 * initial -- A value to use in this Field's initial display. This value
 *            is *not* used as a fallback if data isn't given.
 * helpText -- An optional string to use as "help text" for this Field.
 * error_messages -- An optional dictionary to override the default
 *                   messages that the field will raise.
 * show_hidden_initial -- Boolean that specifies if it is needed to render a
 *                        hidden widget with initial value after widget.
 * validators -- List of additional validators to use
 * localize -- Boolean that specifies if the field should be localized.
 * disabled -- Boolean that specifies whether the field is disabled, that
 *             is its widget is shown in the form but not editable.
 * labelSuffix -- Suffix to be added to the label. Overrides
 *
 * @since 1.1.0
 *
 * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
 */
abstract class Field extends BaseObject implements ContributorInterface
{
    /**
     * @var Form
     */
    public $form;
    public $name;

    /**
     * @var Widget
     */
    public $widget;

    public $required = true;

    /**
     * @var string
     */
    public $htmlName;

    protected $label = null;

    /**
     * The initial value to used when displaying a form that is not bound with data, i.e.
     * before user types in and submits the form.
     *
     * You may be thinking, why not just pass a dictionary of the initial values as data when displaying the form? Well,
     * if you do that, you’ll trigger validation, and the HTML output will include any validation errors
     *
     * Note initial values are not used as “fallback” data in validation if a particular field’s value is not given.
     * initial values are only intended for initial form display:
     *
     * @var null
     */
    public $initial = null;

    /**
     * Any help text that has been associated with the field.
     *
     * @var string
     */
    public $helpText = '';

    /**
     * Boolean that specifies whether the field is disabled, that is its widget is shown in the form but not editable.
     *
     * @var bool
     */
    public $disabled = false;

    public $labelSuffix = null;

    /**
     * A list of some custom validators to run, this provides an easier way of implementing custom validations
     * to your field.
     *
     * @var array
     */
    public $validators = [];

    public function __construct($opts = [])
    {
        $this->validators = [];

        $this->widget = $this->getWidget();
        // replace the default options with the ones passed in.
        foreach ($opts as $key => $value) :
            $this->{$key} = $value;
        endforeach;


        $this->initial = ($this->initial == null) ? [] : $this->initial;
        if (is_string($this->widget)):
            $widget = $this->widget;
            $this->widget = $widget::instance();
        endif;

        $this->widget->isRequired = $this->required;

        // Hook into this->widgetAttrs() for any Field-specific HTML attributes.
        $extra_attrs = $this->widgetAttrs($this->widget);

        if ($extra_attrs):
            $this->widget->attrs = array_merge($this->widget->attrs, $extra_attrs);
        endif;

        if (!is_array($this->validators)):
            throw new ValueError(' { validators } is expected to be an array of validation to apply on the field');
        endif;

        $this->validators = array_merge($this->getDefaultValidators(), $this->validators);
    }

    public static function instance($opts = [])
    {
        return new static($opts);
    }

    /**
     * Default validators.
     *
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public function getDefaultValidators()
    {
        return [];
    }

    public function prepareValue($value)
    {
        return $value;
    }

    /**
     * Given a Widget instance, returns an associative array of any HTML attributes
     * that should be added to the Widget, based on this Field. this is a good place to ensure that the attributes field
     * matches to there related html attributes e.g for form field we get mx_length but html expects maxlength.
     *
     * @param Widget $widget
     *
     * @return array
     *
     * @since 1.0.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public function widgetAttrs(Widget $widget)
    {
        return [];
    }

    /**
     * Returns the Widget to use for this form field.
     * @return Widget
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public function getWidget()
    {
        return TextInput::instance();
    }

    public function toPhp($value)
    {
        return $value;
    }

    public function validators()
    {
        return $this->validators;
    }

    /**
     * Wraps the given contents in a <label>.
     *
     * labelSuffix allows overriding the form's label_suffix.
     *
     * @return string
     *
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public function labelTag($labelSuffix = null)
    {
        $labelText = $this->getLabelName();
        // if the field is not hidden field set label
        if ($this->widget->isHidden()) :
            return '';
        endif;

        if (empty($labelSuffix)):
            $labelSuffix = ($this->labelSuffix) ? $this->labelSuffix : $this->form->labelSuffix;
        endif;

        //Only add the suffix if the label does not end in punctuation.
        if (!in_array(substr("testers", -1), [':', '?', '.', '!'])):
            $labelText = sprintf("%s%s", $labelText, $labelSuffix);
        endif;

        return $this->_formLabel($labelText, $this->getIdForLabel(), []);
    }

    public function _formLabel($labelText = '', $id = '', array $attributes = [])
    {
        $attrs = "";
        if (!empty($id)) :
            $attrs .= sprintf(' for="%s" ', $id);
        endif;

        if (is_array($attributes) && count($attributes) > 0) :
            foreach ($attributes as $key => $val) :
                $attrs .= sprintf(' %s ="%s" ', $key, $val);
            endforeach;;
        endif;

        return sprintf('<label %s > %s</label>', $attrs, $labelText);
    }

    /**
     * Returns the label name .
     *
     * @return mixed|string
     */
    public function getLabelName()
    {
        // incase form label is not set
        if (empty($this->label)):
            return str_replace('_', ' ', ucwords(strtolower($this->name)));
        endif;

        return ucfirst($this->label);
    }

    /**
     * Calculates and returns the ID attribute for this BoundField, if the associated Form has specified autoId.
     * Returns an empty string otherwise.
     *
     * @return string
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public function getAutoId()
    {
        if (is_string($this->form->autoId) && strpos($this->form->autoId, '%s')):
            return sprintf($this->form->autoId, $this->htmlName);
        endif;

        if (is_bool($this->form->autoId) && $this->form->autoId):
            return $this->htmlName;
        endif;

        return '';
    }

    /**
     * The html label ID that will be used for this field.
     *
     * @return mixed
     *
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public function getIdForLabel()
    {
        $id = $this->getAutoId();

        if (ArrayHelper::hasKey($this->widget->attrs, 'id')) :
            $id = ArrayHelper::getValue($this->widget->attrs, 'id');
        endif;

        return $this->widget->getIdForLabel($id);
    }

    /**
     * Return the value that should be shown for this field on render of a bound form, given the submitted POST data for
     * the field and the initial data, if any.
     *
     * For most fields, this will simply be data; FileFields need to handle it a bit differently.
     *
     * @param $data
     * @param $initial
     * @return mixed
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public function boundData($data, $initial)
    {
        if ($this->disabled):
            return $initial;
        endif;
        return $data;
    }

    /**
     * @param string $name
     * @param Form $object
     *
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public function contributeToClass($name, $object)
    {
        $this->setFromName($name);
        $object->loadField($this);
        $this->form = $object;
        $this->htmlName = $this->form->addPrefix($this->name);
    }

    /**
     * The name of the field that will be used in the input element’s name field i.e
     * Returns the name to use in widgets, this is meant to help prepare names for fields like checkbox that take
     * the name as an array.
     *
     * @return mixed
     *
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public function getHtmlName()
    {
        return $this->htmlName;
    }

    /**
     * @param $value
     * @return mixed
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public function clean($value)
    {
        $value = $this->toPhp($value);
        $this->validate($value);
        $this->runValidators($value);

        return $value;
    }

    /**
     * Some validations that the CI_Validator does not take care off
     * This method should raise a ValiationError Exception if the field fails validation.
     *
     * @param $value
     *
     * @throws ValidationError
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public function validate($value)
    {
        if (empty($value) && $this->required):
            throw new ValidationError(sprintf('The field %s is required', $this->name), 'required');
        endif;
    }

    /**
     * Runs custom validation not provided by CI_Validator.
     *
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public function runValidators($value)
    {

        // collect all validation errors for this field
        $validationErrors = [];

        foreach ($this->validators as $validator) :

            try {
                $validator($value);
            } catch (ValidationError $error) {
                $validationErrors = array_merge($validationErrors, $error->getErrorList());
            }
        endforeach;

        if (!empty($validationErrors)):
            throw new ValidationError($validationErrors);
        endif;
    }

    public function setFromName($name)
    {
        $this->name = $name;
        $this->label = $this->getLabelName();
    }

    public function asWidget(Widget $widget = null, $attrs = [], $only_initial = null)
    {
        if ($widget == null):
            $widget = $this->widget;
        endif;

        if ($this->disabled):
            $attrs['disabled'] = true;
        endif;

        if (!empty($this->getAutoId()) &&
            !array_key_exists('id', $attrs) &&
            !array_key_exists('id', $this->widget->attrs)
        ):
            $attrs['id'] = $this->getAutoId();
        endif;

        return (string)$widget->render($this->getHtmlName(), $this->value(), $attrs);
    }

    /**
     * The value of the field.
     *
     * @return mixed
     *
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public function value()
    {
        $data = $this->getInitial();

        if ($this->form->isBound):
            $data = $this->boundData($this->data(), $data);
        endif;

        return $this->prepareValue($data);
    }

    public function data()
    {
        return $this->widget->valueFromDataCollection($this->form->data, $this->form->files, $this->getHtmlName());
    }

    /**
     * Returns error that relate to this field.
     *
     * @return ErrorList
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public function getErrors()
    {
        return ArrayHelper::getValue($this->form->errors(), $this->name, ErrorList::instance());
    }

    /**
     *  attribute is True if the form field is a hidden field and False otherwise.
     *
     * @return mixed
     *
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public function isHidden()
    {
        return $this->widget->isHidden();
    }

    public function __toString()
    {
        try {
            return $this->asWidget();
        } catch (\Exception $exception) {
            Tools::convertExceptionToError($exception);
        }
        return "";
    }

    /**
     * @return string
     */
    public function getHelpText()
    {
        return $this->helpText;
    }

    private function getInitial()
    {
        return $this->form->getInitialForField($this, $this->name);
    }

}
