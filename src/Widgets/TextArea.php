<?php
/**
 * Created by http://eddmash.com
 * User: eddmash
 * Date: 7/16/16
 * Time: 2:12 PM.
 */

namespace Eddmash\PowerOrm\Form\Widgets;

/**
 * Text area: <textarea>...</textarea>.
 *
 * Class TextArea
 *
 * @since 1.1.0
 *
 * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
 */
class TextArea extends Widget
{
    public function __construct($attrs = [])
    {
        $default_attrs = ['cols' => '40', 'rows' => '10'];
        if ($attrs):
            $attrs = array_merge($default_attrs, $attrs);
        endif;
        parent::__construct($attrs);
    }

    public function render($name, $value, $attrs = [])
    {
        $finalAttrs = $this->buildAttrs($attrs, ['name' => $name]);

        $finalVal = '';
        if (!empty($value)):
            $finalVal = $this->prepareValue($value);
        endif;

        return sprintf('<textarea %s>%s</textarea>', $this->flatAttrs($finalAttrs), $finalVal);
    }
}
