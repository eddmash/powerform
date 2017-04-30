<?php
/**
 * This file is part of the ci4 package.
 *
 * (c) Eddilbert Macharia (http://eddmash.com)<edd.cowan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eddmash\PowerOrm\Form\Validations;

use Eddmash\PowerOrm\Exception\ValidationError;
use Respect\Validation\Validator;

class MaxLengthValidator extends BaseValidator
{
    public $maxLength;

    /**
     * {@inheritdoc}
     */
    public function __invoke($value)
    {
        if (!Validator::length(null, $this->maxLength, true)->validate($value)) :
            throw new ValidationError(
                sprintf(
                    'Ensure this value has at most %s character (it has %s)',
                    $this->maxLength, strlen($value)
                ),
                'max_length'
            );
        endif;
    }

}
