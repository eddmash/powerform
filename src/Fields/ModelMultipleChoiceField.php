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

use Eddmash\PowerOrm\Exception\ValidationError;
use Eddmash\PowerOrm\Form\Widgets\SelectMultiple;

class ModelMultipleChoiceField extends ModelChoiceField
{
    public $defaultErrorMessages = [
        'list' => 'Enter an array of values.',
        'invalid_choice' => 'Select a valid choice. %s is not one of the available choices.',
        'invalid_pk_value' => '"%s" is not a valid value for a primary key.',
    ];

    public function getWidget()
    {
        return SelectMultiple::instance();
    }

    public function prepareValue($value)
    {
        if ((is_array($value))
            || (is_iterable($value) && is_object($value) && !property_exists($value, 'meta'))
        ):
            $v = [];
            foreach ($value as $item) :
                $v[] = parent::prepareValue($item);
            endforeach;

            return $v;
        endif;

        return parent::prepareValue($value);
    }

    /**
     * @inheritDoc
     */
    public function clean($value)
    {
        $value = $this->prepareValue($value);
        if ($this->required && empty($value)) :
            throw new ValidationError($this->errorMessages['required'], 'required');
        elseif (!$this->required && empty($value)):
            return [];
        endif;

        if (!is_array($value)) :
            throw new ValidationError($this->errorMessages['list'], 'list');
        endif;
        $qs = $this->checkValues($value);
        $this->runValidators($value);
        return $qs;
    }

    /**
     * Given a list of possible PK values, returns a QuerySet of the corresponding objects. Raises a ValidationError
     * if a given value is  invalid (not a valid PK, not in the queryset, etc.)
     * @param $values
     * @return \Eddmash\PowerOrm\Model\Query\Queryset
     * @author: Eddilbert Macharia (http://eddmash.com)<edd.cowan@gmail.com>
     */
    private function checkValues($values)
    {

        $key = ($this->valueField)?$this->valueField:'pk';

        $fkey = sprintf("%s__in",$key);

        $qs = $this->queryset->filter([$fkey=>$values]);

        $pks = [];
//        foreach ($qs as $object) :
//            $object->pk;
////            $pks[] = $object->{$key};
//        endforeach;
//        foreach ($values as $value) :
//            if (!in_array($value, $pks)) :
//                throw new ValidationError(
//                    sprintf($this->errorMessages['invalid_choice'], $value),
//                    'invalid_choice'
//                );
//            endif;
//        endforeach;

        return $qs;
    }


}