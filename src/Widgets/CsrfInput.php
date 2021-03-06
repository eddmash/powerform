<?php
/**
 * This file is part of the ci4 package.
 *
 * (c) Eddilbert Macharia (http://eddmash.com)<edd.cowan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eddmash\PowerOrm\Form\Widgets;

class CsrfInput extends TextInput
{
    public $multiple_selected = true;

    /**
     * {@inheritdoc}
     */
    public function render($name, $value, $attrs = [])
    {
        $finalAttrs = $this->buildAttrs($attrs, ['type' => $this->inputType, 'name' => $name]);
        // if we have value , add it
        if (!empty($value)):
            $finalAttrs['value'] = $this->formatValue($value);
        endif;

        return sprintf('<input %s>', $this->flatAttrs($finalAttrs));
    }

    /**
     * {@inheritdoc}
     */
    public function valueFromDataCollection($data, $files, $name)
    {
        dump($name);

        return parent::valueFromDataCollection($data, $files, $name); // TODO: Change the autogenerated stub
    }
}
