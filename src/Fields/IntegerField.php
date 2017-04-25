<?php
/**
 * Created by http://eddmash.com
 * User: eddmash
 * Date: 7/16/16
 * Time: 2:08 PM.
 */

namespace Eddmash\PowerOrm\Form\Fields;

use Eddmash\PowerOrm\Form\Widgets\NumberInput;
use Eddmash\PowerOrm\Form\Widgets\Widget;

/**
 * Creates a:
 *      Default widget: NumberInput.
 *      Empty value: None.
 *
 * Validates that the given value is an integer.
 *
 *
 * Takes two optional arguments for validation:
 *  - maxValue
 *  - minValue
 *
 * These control the range of values permitted in the field.
 *
 * Class IntegerField
 *
 * @since 1.1.0
 *
 * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
 */
class IntegerField extends Field
{
    public $minValue;
    public $maxValue;

    public function __construct($opts = [])
    {
        parent::__construct($opts);

        if ($this->maxValue):
            $this->validators[] = sprintf('greater_than[%s]', $this->maxValue);
        endif;

        if ($this->minValue):
            $this->validators[] = sprintf('less_than[%s]', $this->minValue);
        endif;
    }

    public function getWidget()
    {
        return NumberInput::instance();
    }

    /**
     * {@inheritdoc}
     */
    public function widgetAttrs(Widget $widget)
    {
        $attrs = parent::widgetAttrs($widget);

        if ($this->maxValue):
            $attrs['max'] = $this->maxValue;
        endif;

        if ($this->minValue):
            $attrs['min'] = $this->minValue;
        endif;

        return $attrs;
    }
}
