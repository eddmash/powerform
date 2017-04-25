<?php
/**
 * Created by http://eddmash.com
 * User: eddmash
 * Date: 7/16/16
 * Time: 2:10 PM.
 */

namespace Eddmash\PowerOrm\Form\Widgets;

/**
 * base class for all input widgets, should never initialized
 * Class Input.
 *
 * @since 1.1.0
 *
 * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
 */
abstract class Input extends Widget
{
    public $inputType = null;

    public function render($name, $value, $attrs = [], $kwargs = [])
    {
        $finalAttrs = $this->buildAttrs($attrs, ['type' => $this->inputType, 'name' => $name]);
        // if we have value , add it
        if (!empty($value)):
            $finalAttrs['value'] = $this->prepareValue($value);
        endif;

        return sprintf('<input %s>', $this->flatAttrs($finalAttrs));
    }
}
