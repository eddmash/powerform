<?php
/**
 * Created by http://eddmash.com
 * User: eddmash
 * Date: 7/3/16
 * Time: 10:13 AM.
 */

namespace Eddmash\PowerOrm\Form;

use Eddmash\PowerOrm\BaseOrm;
use Eddmash\PowerOrm\Model\Model;
use Orm;

/**
 * @param Model $model
 * @param $required_fields
 * @param $excludes
 * @param $widgets
 * @param $labels
 * @param $helpTexts
 * @param $field_classes
 *
 * @return array
 *
 * @since 1.1.0
 *
 * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
 */
function fields_from_model(Model $model, $required_fields, $excludes, $widgets, $labels, $helpTexts, $field_classes)
{
    $model_fields = $model->meta->getConcreteFields();
    $fields = [];
    foreach ($model_fields as $name => $obj) :
        if (in_array($name, $excludes)):
            continue;
        endif;

        if (!empty($required_fields) && !in_array($name, $required_fields)):
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

        if (!empty($field_classes) && array_key_exists($name, $field_classes)):
            $kwargs['form_class'] = $field_classes[$name];
        endif;

        $fields[$name] = $obj->formfield();
    endforeach;

    return $fields;
}

class BaseModelForm extends BaseForm
{
    public $model;
    protected $fields = [];
    protected $excludes = [];
    protected $labels = [];
    protected $widgets = [];
    protected $helpTexts = [];
    protected $field_classes = [];

    public function setup()
    {
        $model = BaseOrm::getRegistry()->getModel($this->model);
        $fields = fields_from_model($model, $this->fields, $this->excludes,
            $this->widgets, $this->labels, $this->helpTexts, $this->field_classes
        );

        foreach ($fields as $name => $value) :
            // if field is already in the fields, that takes precedence over model field name
            if (array_key_exists($name, $this->fields)):
                continue;
            endif;

            $this->{$name} = $value;
        endforeach;

        parent::setup();
    }

    public function custom()
    {
    }

    public function model($model = null)
    {
        $this->model = (!empty($model)) ? $model : $this->model;

        if (is_string($this->model)):
            Orm::ci_instance()->load->model($this->model);
            $this->model = Orm::ci_instance()->{$this->model};
        endif;

        return $this;
    }

    public function only($fields = [])
    {
        $this->fields = array_merge($this->fields, $fields);

        return $this;
    }

    public function exclude($excludes = [])
    {
        $this->excludes = array_merge($this->excludes, $excludes);

        return $this;
    }

    public function labels($labels = [])
    {
        $this->labels = array_merge($this->labels, $labels);

        return $this;
    }

    public function widgets($widgets = [])
    {
        $this->widgets = array_merge($this->widgets, $widgets);

        return $this;
    }

    public function helpTexts($helpTexts = [])
    {
        $this->helpTexts = array_merge($this->helpTexts, $helpTexts);

        return $this;
    }

    public function field_classes($field_classes = [])
    {
        $this->field_classes = array_merge($this->field_classes, $field_classes);

        return $this;
    }
}
