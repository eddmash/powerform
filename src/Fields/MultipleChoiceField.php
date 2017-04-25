<?php
/**
 * Created by http://eddmash.com
 * User: eddmash
 * Date: 7/16/16
 * Time: 2:09 PM.
 */

namespace Eddmash\PowerOrm\Form\Fields;

use Eddmash\PowerOrm\Form\Widgets\SelectMultiple;

class MultipleChoiceField extends ChoiceField
{
    public function getWidget()
    {
        return SelectMultiple::instance();
    }
}
