<?php
/**
 * Created by http://eddmash.com
 * User: eddmash
 * Date: 7/16/16
 * Time: 2:13 PM.
 */

namespace Eddmash\PowerOrm\Form\Widgets;

use Eddmash\PowerOrm\Helpers\ArrayHelper;

/**
 * Select widget: <select><option ...>...</select>.
 *
 * Options:
 *
 *  choices
 *
 *      This attribute is optional when the form field does not have a choices attribute.
 *      If it does, it will override anything you set here when the attribute is updated on the Field.
 *
 * Class Select
 *
 * @since 1.1.0
 *
 * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
 */
class Select extends Widget
{
    public $multiple_selected = false;
    public $open_select = '<select %s >';

    public $choices = [];

    public function __construct($attrs = [])
    {

        if (ArrayHelper::hasKey($attrs, 'choices')):
            $this->choices = ArrayHelper::pop($attrs, 'choices');
        endif;

        parent::__construct($attrs);
    }

    public function render($name, $value, $attrs = [])
    {
        if (empty($value)):
            // in case its null, false etc
            $value = [];
        endif;

        $finalAttrs = $this->buildAttrs($attrs, ['name' => $name]);
        $output = [];
        // open select
        $output[] = sprintf($this->open_select, $this->flatAttrs($finalAttrs));
        // add select options
        $options[] = $this->render_options($value);

        if (!empty($options)):
            $output = array_merge($output, $options);
        endif;
        // close select
        $output[] = '</select>';

        return implode(' ', $output);
    }

    public function _prepare_selected($selected)
    {
        return (is_array($selected) && empty($selected)) ? $selected : [$selected];
    }

    public function render_options($selected_choices)
    {
        $selected_choices = $this->_prepare_selected($selected_choices);

        /*
         * 'choices'=>[
         *      'gender'=> ['f'=>'Female', 'm'=>'Male' ],
         *      'bmw'=>'mercedes benz'
         * ]
         */
        $choices = $this->choices;

        if (is_callable($choices)):
            $choices = call_user_func($choices);
        endif;

        $output = [];

        foreach ($choices as $label => $value) :

            if (is_array($value)):

                $output[] = sprintf('<optgroup label="%s">', $label);

                foreach ($value as $c_label => $c_value) :

                    $output[] = $this->render_option($selected_choices, $c_value, $c_label);

                endforeach;

                $output[] = '</optgroup>';
            else:

                $output[] = $this->render_option($selected_choices, $value, $label);
            endif;

        endforeach;

        return implode(' ', $output);
    }

    public function render_option($selected_choices, $label, $value)
    {
        $selected_html = '';

        if (in_array($value, $selected_choices)):
            $selected_html = 'selected="selected"';
        endif;

        return sprintf('<option value="%1$s" %2$s >%3$s</option>', $value, $selected_html, $label);
    }
}
