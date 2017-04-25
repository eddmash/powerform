<?php
/**
 * Created by http://eddmash.com
 * User: eddmash
 * Date: 7/16/16
 * Time: 2:07 PM.
 */

namespace Eddmash\PowerOrm\Form\Fields;

use Eddmash\PowerOrm\Form\Widgets\EmailInput;

/**
 * Creates an :
 *      Default widget: EmailInput
 *      Empty value: '' (an empty string)
 *      Validates that the given value is a valid email address.
 *
 * Has two optional arguments for validation, maxLength and minLength.
 * If provided, these arguments ensure that the string is at most or at least the given length.
 *
 * Class EmailField
 *
 * @since 1.1.0
 *
 * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
 */
class EmailField extends CharField
{
    public $defaultValidators = ['valid_email'];

    public function getWidget()
    {
        return EmailInput::instance();
    }
}
