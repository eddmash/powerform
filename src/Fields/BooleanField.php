<?php
/**
 * Created by http://eddmash.com
 * User: eddmash
 * Date: 7/16/16
 * Time: 2:08 PM.
 */

namespace Eddmash\PowerOrm\Form\Fields;

use Eddmash\PowerOrm\Form\Widgets\CheckboxInput;

/**
 * Creates a :
 *       Default widget: CheckboxInput
 *       Empty value: False
 *       Validates that the value is True (e.g. the check box is checked) if the field has required=True.
 *
 * Class BooleanField
 *
 * @since 1.1.0
 *
 * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
 */
class BooleanField extends Field
{
    public function getWidget()
    {
        return CheckboxInput::instance();
    }
}
