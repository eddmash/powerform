<?php
/**
 * Created by http://eddmash.com
 * User: eddmash
 * Date: 7/16/16
 * Time: 2:13 PM.
 */

namespace Eddmash\PowerOrm\Form\Widgets;

/**
 * This creates a single checkbox on the fields, to create more than one use {@see MultipleCheckboxes}.
 *
 * Checkbox: <input type='checkbox' ...>
 *
 * Class CheckboxInput
 *
 * @since 1.1.0
 *
 * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
 */
class CheckboxInput extends Widget
{
    public function render($name, $value, $attrs = [], $kwargs = [])
    {
        $finalAttrs = $this->buildAttrs($attrs, ['type' => 'checkbox', 'name' => $name]);

        // if we have value , add it
        // but since we are dealing with checkbox, this will be checked
        if ($this->isChecked($value)):
            $finalAttrs['checked'] = 'checked';
        endif;

        return sprintf('<input %s>', $this->flatAttrs($finalAttrs));
    }

    public function isChecked($value)
    {
        return !empty($value);
    }

    public function valueFromDataCollection($data, $name)
    {
        // checkboxes are either checked or not checked they don't take values like other input fields
        if (!array_key_exists($name, $data)):
            return false;
        endif;

        $value = $data[$name];

        if (is_bool($value)):
            return $value;
        endif;

        // type cast otherwise
        return (bool) $value;
    }
}
