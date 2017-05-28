<?php
/**
 * This file is part of the ci4 package.
 *
 * (c) Eddilbert Macharia (http://eddmash.com)<edd.cowan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eddmash\PowerOrm\Form\Helpers;

use Eddmash\PowerOrm\Form\Fields\Field;
use Eddmash\PowerOrm\Model\Model;

class ModelChoiceIterator
{
    protected $queryset;
    /**
     * @var Field
     */
    private $field;

    public function __construct(Field $field)
    {
        $this->queryset = $field->getQueryset();
        $this->field = $field;
    }

    public function __invoke()
    {
        $opts = [];
        $models = $this->queryset->all();
        foreach ($models as $object) :
            $choices = $this->choices($object);
            $opts[$choices[0]] = $choices[1];
        endforeach;

        return $opts;
    }

    public function choices(Model $instance)
    {
        if ($this->field->labelField) :
            $label = $instance->{$this->field->labelField};
        else:
            $label = (string) $instance;
        endif;

        return [$this->field->prepareValue($instance), $label];
    }

}
