<?php
/**
 * This file is part of the ci4 package.
 *
 * (c) Eddilbert Macharia (http://eddmash.com)<edd.cowan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Eddmash\PowerOrm\Form\Fields;

use Eddmash\PowerOrm\Exception\KeyError;
use Eddmash\PowerOrm\Exception\NotImplemented;
use Eddmash\PowerOrm\Exception\ValidationError;
use Eddmash\PowerOrm\Form\Widgets\MultiWidget;
use Eddmash\PowerOrm\Helpers\ArrayHelper;

/**
 * Creates a Csrf input.
 *
 * @package Eddmash\PowerOrm\Form\Widgets
 * @author: Eddilbert Macharia (http://eddmash.com)<edd.cowan@gmail.com>
 */
class MultiValueField extends Field
{
    public $fields;
    public $defaultErrorMessages = [
        'invalid' => 'Enter a list of values.',
        'incomplete' => 'Enter a complete value.',
    ];
    public $requireAllFields;

    /**
     * @var MultiWidget
     */
    public $widget;

    public function __construct(array $attrs = [])
    {
        $this->requireAllFields = ArrayHelper::pop($attrs, 'requireAllFields', true);
        $fields = ArrayHelper::pop($attrs, 'fields', []);
        parent::__construct($attrs);

        $initial = [];
        /**@var $fields Field[] */
        foreach ($fields as $index => &$field) :
            $field->defaultErrorMessages['incomplete'] = $this->errorMessages['incomplete'];
            if ($this->requireAllFields) :
                //Set 'required' to False on the individual fields, because the
                // required validation will be handled by MultiValueField, not
                // by those individual fields.
                $field->required = false;
            endif;
            $initial[$index] = $field->initial;
        endforeach;
        $this->fields = $fields;
        $this->initial = $initial;

        $this->loadSubWidgets();
    }

    /**
     * @inheritDoc
     */
    public function getWidget()
    {
        return MultiWidget::instance();
    }


    /**
     * @inheritDoc
     */
    public function clean($value)
    {
        $errors = $cleanData = [];
        if (empty($value) || !is_array($value)) :
            if ($this->required) :
                throw new ValidationError($this->errorMessages['required'], 'required');
            else:
                return $this->compress([]);
            endif;
        else:
//            throw new ValidationError($this->errorMessages['invalid'], 'invalid');
        endif;

        foreach ($this->fields as $index => $field) :

            try {
                $fieldVal = ArrayHelper::getValue($value, $index, ArrayHelper::STRICT);
            } catch (KeyError $keyError) {
                $fieldVal = null;
            }
            if (empty($fieldVal)):
                if ($this->requireAllFields):
                    # Raise a 'required' error if the MultiValueField is
                    # required and any field is empty.
                    if ($field->required):
                        throw new  ValidationError($this->errorMessages['required'], 'required');
                    endif;
                elseif ($field->required):
                    // todo Otherwise, add an 'incomplete' error to the list of
                    // collected errors and skip field cleaning, if a required
                    // field is empty.
//                    if field.error_messages['incomplete'] not in errors:
//                        errors.append(field.error_messages['incomplete'])
//                    continue
                endif;
            endif;

            try {
                $cleanData[$index] = $field->clean($fieldVal);
            } catch (ValidationError $error) {
                //todo
            }
        endforeach;
        if ($errors) :
            throw new ValidationError($errors);
        endif;

        $out = $this->compress($cleanData);
        $this->validate($out);
        $this->runValidators($out);

        return $out;
    }

    /**
     * Populates subwidgets to the main widget
     * @author: Eddilbert Macharia (http://eddmash.com)<edd.cowan@gmail.com>
     */
    private function loadSubWidgets()
    {
        $subWidgets = [];
        // get widgets for the fields
        foreach ($this->fields as $index => $field) :
            $subWidgets[$index] = $field->widget;
        endforeach;

        $this->widget->setSubWidgets($subWidgets);
    }

    /**
     * Returns a single value for the given list of values. The values can be assumed to be valid.
     * @param $cleanData
     * @throws NotImplemented
     * @author: Eddilbert Macharia (http://eddmash.com)<edd.cowan@gmail.com>
     */
    public function compress($cleanData)
    {
        throw new NotImplemented('Subclasses must implement this method.');
    }


}