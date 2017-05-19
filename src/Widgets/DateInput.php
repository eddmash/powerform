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

use Eddmash\PowerOrm\Form\Helpers\Tools;

class DateInput extends TextInput
{
    public $inputType = 'date';
    public $format;

    public function formatValue($value)
    {
        $format = ($this->format) ? $this->format : Tools::getDateFormats()[0];
        if (is_string($value)):
            return $value;
        endif;

        if ($value instanceof \DateTime) :
            return $value->format($format);
        endif;

        return $value;

    }
}
