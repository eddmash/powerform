<?php
/**
 * Created by http://eddmash.com
 * User: eddmash
 * Date: 7/3/16
 * Time: 10:13 AM.
 */

namespace Eddmash\PowerOrm\Form;

use Eddmash\PowerOrm\BaseOrm;
use Eddmash\PowerOrm\Form\Fields\Field;
use Eddmash\PowerOrm\Model\Model;

/**
 * @param Model $model
 * @param $requiredFields
 * @param $excludes
 * @param $widgets
 * @param $labels
 * @param $helpTexts
 * @param $fieldClasses
 *
 * @return Field[]
 *
 * @since 1.1.0
 *
 * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
 */
function fieldsFromModel(Model $model, $requiredFields, $excludes, $widgets, $labels, $helpTexts, $fieldClasses)
{
    $modelFields = $model->meta->getConcreteFields();
    $fields = [];
    foreach ($modelFields as $name => $field) :
        if (in_array($name, $excludes)):
            continue;
        endif;

        if (!empty($requiredFields) && !in_array($name, $requiredFields)):
            continue;
        endif;
        $kwargs = [];
        if (!empty($widgets) && array_key_exists($name, $widgets)):
            $kwargs['widget'] = $widgets[$name];

        endif;

        if (!empty($labels) && array_key_exists($name, $labels)):
            $kwargs['label'] = $labels[$name];
        endif;

        if (!empty($helpTexts) && array_key_exists($name, $helpTexts)):
            $kwargs['helpText'] = $helpTexts[$name];
        endif;

        if (!empty($fieldClasses) && array_key_exists($name, $fieldClasses)):
            $kwargs['fieldClass'] = $fieldClasses[$name];
        endif;

        $fields[$name] = $field->formfield($kwargs);
    endforeach;

    return $fields;
}

abstract class ModelForm extends Form
{
    protected $modelClass;
    protected $fields = [];
    protected $excludes = [];
    private $labels = [];
    private $widgets = [];
    private $helpTexts = [];
    private $fieldClasses = [];

    public function setup()
    {
        $fields = fieldsFromModel(
            $this->getModel(),
            $this->fields,
            $this->excludes,
            $this->widgets(),
            $this->labels(),
            $this->helpTexts(),
            $this->fieldClasses()
        );

        foreach ($fields as $name => $field) :
            // if field is already in the fields, that takes precedence over model field name
            if (array_key_exists($name, $this->fields)):
                continue;
            endif;

            $this->{$name} = $field;
        endforeach;

        parent::setup();
    }

    /**
     * Widgets to use on the fields.
     *
     * @author: Eddilbert Macharia (http://eddmash.com)<edd.cowan@gmail.com>
     */
    public function widgets()
    {
        return $this->widgets;
    }
    /**
     * Field classes to use on the fields.
     *
     * @author: Eddilbert Macharia (http://eddmash.com)<edd.cowan@gmail.com>
     */
    public function fieldClasses()
    {
        return $this->fieldClasses;
    }
    /**
     * Help texts classes to use on the fields.
     *
     * @author: Eddilbert Macharia (http://eddmash.com)<edd.cowan@gmail.com>
     */
    public function helpTexts()
    {
        return $this->helpTexts;
    }
    /**
     * Label to use on the fields.
     *
     * @author: Eddilbert Macharia (http://eddmash.com)<edd.cowan@gmail.com>
     */
    public function labels()
    {
        return $this->labels;
    }

    /**
     * Returns an instance of the model.
     *
     * @return Model
     * @author: Eddilbert Macharia (http://eddmash.com)<edd.cowan@gmail.com>
     */
    public function getModel()
    {
        return BaseOrm::getRegistry()->getModel($this->getModelClass());
    }

    /**
     * Returns the Model class to use in this form.
     *
     * @return mixed
     * @author: Eddilbert Macharia (http://eddmash.com)<edd.cowan@gmail.com>
     */
    public function getModelClass()
    {
        return $this->modelClass;
    }
}
