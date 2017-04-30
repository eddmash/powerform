<?php

namespace Eddmash\PowerOrm\Form\Fields;

use Eddmash\PowerOrm\Form\Validations\MaxLengthValidator;
use Eddmash\PowerOrm\Form\Validations\MinLengthValidator;
use Eddmash\PowerOrm\Form\Widgets\Widget;

/**
 * Creates a :
 *      Default widget: TextInput
 *      Empty value: '' (an empty string)
 *      Validates maxLength or minLength, if they are provided. Otherwise, all inputs are valid.
 *
 * Has two optional arguments for validation:
 *  - maxLength
 *  - minLength
 *
 *  If provided, these arguments ensure that the string is at most or at least the given length.
 *
 * Class CharField
 *
 * @since 1.1.0
 *
 * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
 */
class CharField extends Field
{
    public $maxLength;
    public $minLength;

    public function __construct($opts = [])
    {
        parent::__construct($opts);

        if ($this->maxLength):
            $this->validators[] = MaxLengthValidator::instance(['maxLength' => $this->maxLength]);
        endif;

        if ($this->minLength):
            $this->validators[] = MinLengthValidator::instance(['minLength' => $this->minLength]);
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function widgetAttrs(Widget $widget)
    {
        $attrs = parent::widgetAttrs($widget);
        if ($this->maxLength):
            $attrs['maxlength'] = $this->maxLength;
        endif;

        return $attrs;
    }
}
