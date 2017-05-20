<?php
/**
 * Created by http://eddmash.com
 * User: eddmash
 * Date: 7/3/16
 * Time: 10:13 AM.
 */

namespace Eddmash\PowerOrm\Form;

use Eddmash\PowerOrm\BaseOrm;
use Eddmash\PowerOrm\Exception\ImproperlyConfigured;
use Eddmash\PowerOrm\Exception\ValidationError;
use Eddmash\PowerOrm\Exception\ValueError;
use Eddmash\PowerOrm\Form\Fields\Field;
use Eddmash\PowerOrm\Helpers\ArrayHelper;
use Eddmash\PowerOrm\Model\Field\AutoField;
use Eddmash\PowerOrm\Model\Model;

function getValuesFromModelInstance(Model $model, array $fields, array $exclude)
{
    $values = [];
    foreach ($model->meta->getConcreteFields() as $concreteField) :
        if ($fields && !in_array($concreteField->getName(), $fields)) :
            continue;
        endif;
        if ($exclude && in_array($concreteField->getName(), $exclude)) :
            continue;
        endif;

        $values[$concreteField->getName()] = $concreteField->valueFromObject($model);
    endforeach;

    return $values;
}

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

        if($fieldClass = $field->formfield($kwargs)):

            $fields[$name] = $fieldClass;
        endif;
    endforeach;

    return $fields;
}

/**
 * Populates the model instance with the cleanedData.
 *
 * @param Model $model
 * @param $data
 *
 * @return Model
 * @author: Eddilbert Macharia (http://eddmash.com)<edd.cowan@gmail.com>
 */
function populateModelInstance(Model $model, Form $form)
{
    foreach ($model->meta->getFields() as $field) :
        if (!ArrayHelper::hasKey($form->cleanedData, $field->getName()) || $field instanceof AutoField) :
            continue;
        endif;

        $field->saveFromForm($model, $form->cleanedData[$field->getName()]);
    endforeach;

    return $model;
}

abstract class ModelForm extends Form
{
    private $modelInstance;
    protected $modelClass;
    protected $modelFields = [];
    protected $excludes = [];
    private $labels = [];
    private $widgets = [];
    private $helpTexts = [];
    private $fieldClasses = [];

    /**
     * {@inheritdoc}
     */
    public function __construct($kwargs = [])
    {

        if (is_null($this->getModelClass())):
            throw new ValueError('ModelForm has no model class specified.');
        endif;

        if (empty($this->modelFields) && empty($this->excludes)):
            throw new ImproperlyConfigured(
                sprintf(
                    "Creating a ModelForm without either the 'modelFields' ".
                    "attribute or the 'exclude' attribute is prohibited; form %s needs updating.",
                    static::class
                )
            );
        endif;

        if ($this->modelFields === "__all__"):
            $this->modelFields = [];
        endif;

        $instance = ArrayHelper::pop($kwargs, 'instance', null);

        $initial = ArrayHelper::getValue($kwargs, 'initial', []);

        if ($instance) :
            $this->modelInstance = $instance;
            $modelInitial = getValuesFromModelInstance($this->modelInstance, $this->modelFields, $this->excludes);
            $initial = array_merge($modelInitial, $initial);
        else:
            $this->modelInstance = $this->getModel();
        endif;

        $kwargs['initial'] = $initial;

        parent::__construct($kwargs);

    }

    /**{@inheritdoc}*/
    public function setup()
    {
        $fields = fieldsFromModel(
            $this->getModel(),
            $this->modelFields,
            $this->excludes,
            $this->widgets(),
            $this->labels(),
            $this->helpTexts(),
            $this->fieldClasses()
        );

        foreach ($fields as $name => $field) :
            // if field is already in the fields, that takes precedence over model field name
            if (ArrayHelper::hasKey($this->fields(), $name)):
                continue;
            endif;

            $this->addField($name, $field);
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
     * Help texts to use on the fields.
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
    private function getModel()
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

    /**
     * Return the model instance the form is working on.
     *
     * @return Model
     */
    public function getModelInstance()
    {
        return $this->modelInstance;
    }

    /**
     * {@inheritdoc}
     */
    public function postClean()
    {
        $exclude = [];
        $this->modelInstance = populateModelInstance($this->modelInstance, $this);
        try {
            $this->modelInstance->fullClean($exclude);
        } catch (ValidationError $error) {
            //todo
        }
    }

    /**
     * Save this form's modelInstance object if commit=true. Otherwise, add a saveM2M() method to the form which can
     * be called after the instance is saved manually at a later time.
     *
     * @author: Eddilbert Macharia (http://eddmash.com)<edd.cowan@gmail.com>
     *
     * @param bool $commit
     *
     * @return Model
     *
     * @throws ValueError
     */
    public function save($commit = true)
    {
        $modelInstance = $this->modelInstance;

        if (!$this->errors()->isEmpty()) :
            throw new ValueError(
                sprintf(
                    "The %s could not be %s because the data didn't validate.",
                    $modelInstance->meta->getModelName()
                )
            );
        endif;

        if ($commit) :
            $this->modelInstance->save();
            $this->saveM2M();
        endif;

        return $modelInstance;
    }

    private function saveM2M()
    {

    }

}
