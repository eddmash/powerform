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



use Eddmash\PowerOrm\Form\Widgets\NumberInput;
use Eddmash\PowerOrm\Form\Widgets\Widget;
use Respect\Validation\Validator;

class DecimalField extends IntegerField
{
    public $decimalPlaces;
    public $maxDigits;

    /**
     * @inheritDoc
     */
    public function widgetAttrs(Widget $widget)
    {
        $attrs = parent::widgetAttrs($widget);
       if($widget instanceof NumberInput && !array_key_exists('step', $widget->attrs)):
           $step = 'any';
           if($this->decimalPlaces):
                $step = $this->decimalPlaces;
           endif;
           $attrs['step'] = $step;
       endif;
       return $attrs;
    }

    /**
     * @inheritDoc
     */
    public function getDefaultValidators()
    {
        $validators = parent::getDefaultValidators();
        $validators[] = Validator::floatVal();
        return $validators;
    }


}