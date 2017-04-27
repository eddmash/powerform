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


use Eddmash\PowerOrm\Form\Exception\ValidationError;
use Respect\Validation\Validator;

class MinLengthValidator extends BaseValidator
{
    public $minLength;

    /**
     * @inheritDoc
     */
    function __invoke($value)
    {
        if (!Validator::length(null, $this->minLength, true)->validate($value)) :
            throw new ValidationError(
                sprintf(
                    'Ensure this value has at least %s character (it has %s)',
                    $this->minLength, strlen($value)
                ),
                'max_length'
            );
        endif;;
    }


}