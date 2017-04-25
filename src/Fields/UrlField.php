<?php
/**
 * Created by http://eddmash.com
 * User: eddmash
 * Date: 7/16/16
 * Time: 2:07 PM.
 */

namespace Eddmash\PowerOrm\Form\Fields;

use Eddmash\PowerOrm\Form\Widgets\UrlInput;

/**
 * Creates a:
 *      Default widget: URLInput
 *      Empty value: '' (an empty string)
 *      Validates that the given value is a valid URL.
 *
 *
 * Takes the following optional arguments:
 *      - maxLength
 *      - minLength
 *
 * These are the same as CharField->maxLength and CharField->minLength.
 *
 * Class UrlField
 *
 * @since 1.1.0
 *
 * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
 */
class UrlField extends CharField
{
    public $defaultValidators = ['valid_url'];

    public function getWidget()
    {
        return UrlInput::instance();
    }
}
