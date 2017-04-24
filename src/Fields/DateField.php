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


use Eddmash\PowerOrm\Form\Widgets\DateInput;

class DateField extends Field
{
    public function get_widget()
    {
        return DateInput::instance();
    }
}