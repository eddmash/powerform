<?php
/**
 * Created by http://eddmash.com
 * User: eddmash
 * Date: 7/16/16
 * Time: 2:07 PM.
 */

namespace Eddmash\PowerOrm\Form\Fields;

use Eddmash\PowerOrm\Form\Validations\SlugValidator;

/**
 * Creates a:
 *      Default widget: TextInput
 *      Empty value: '' (an empty string)
 *      Validates that the given value contains only letters, numbers, underscores, and hyphens.
 *
 * This field is intended for use in representing a model SlugField in forms.
 *
 * Class SlugField
 *
 * @since 1.1.0
 *
 * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
 */
class SlugField extends CharField
{
    /**
     * {@inheritdoc}
     */
    public function getDefaultValidators()
    {
        $validators = parent::getDefaultValidators();
        $validators[] = SlugValidator::instance();

        return $validators;
    }
}
