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

use Eddmash\PowerOrm\Exception\FieldDoesNotExist;
use Eddmash\PowerOrm\Exception\ObjectDoesNotExist;
use Eddmash\PowerOrm\Exception\ValidationError;
use Eddmash\PowerOrm\Form\Helpers\ModelChoiceIterator;
use Eddmash\PowerOrm\Helpers\ArrayHelper;
use Eddmash\PowerOrm\Model\Query\Queryset;

class ModelChoiceField extends ChoiceField
{
    /**
     * This optional argument is used to specify the field to use as the value of the choices in the field’s widget.
     *
     * @var
     */
    public $valueField;
    /**
     * This optional argument is used to specify the field to use as the value of the choices in the field’s widget.
     *
     * @var
     */
    public $labelField;

    /**
     * @var Queryset
     */
    public $queryset;

    public $modelIteratorClass;

    public $defaultErrorMessages = [
        'invalid_choice' => 'Select a valid choice. That choice is not one of the available choices.',
    ];

    public function __construct(array $opts = [])
    {
        $queryset = ArrayHelper::pop($opts, 'queryset');

        $this->modelIteratorClass = ArrayHelper::pop($opts, 'modelIteratorClass', ModelChoiceIterator::class);
        parent::__construct($opts);
        $this->setQueryset($queryset);
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        // some has already passed in the choices via constructor
        if ($this->choices) :
            return $this->choices;
        endif;

        $modelClass = $this->modelIteratorClass;

        return new $modelClass($this); // TODO: Change the autogenerated stub
    }

    public function prepareValue($value)
    {
        if (property_exists($value, 'meta')) :
            if ($this->valueField) :
                try {
                    /** @var $field \Eddmash\PowerOrm\Model\Field\Field */
                    $field = $value->getMeta()->getField($this->valueField);

                    // use attribute name this way we get foreignkey id instead of object
                    return $value->{$field->getAttrName()};
                } catch (FieldDoesNotExist $fieldDoesNotExist) {
                    return $value->{$this->valueField};
                }
            else:
                return $value->pk;
            endif;
        endif;

        return parent::prepareValue($value);
    }

    /**
     * @return mixed
     */
    public function getQueryset()
    {
        return $this->queryset;
    }

    /**
     * @param mixed $queryset
     */
    public function setQueryset($queryset)
    {
        $this->queryset = $queryset;
        $this->widget->choices = $this->getChoices();
    }

    /**{@inheritdoc}*/
    public function toPhp($value)
    {
        if (empty($value)) :
            return;
        endif;

        try {
            $fieldName = ($this->valueField) ? $this->valueField : 'pk';
            $value = $this->getQueryset()->get([$fieldName => $value]);
        } catch (ObjectDoesNotExist $exception) {
            throw new ValidationError($this->errorMessages['invalid_choice'], $this->code = 'invalid_choice');
        }

        return $value;
    }

}
