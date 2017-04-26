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

class EmailValidator extends BaseValidator
{
    /**
     * @inheritDoc
     */
    function __invoke($value)
    {
        if (!Validator::email()->validate($value)) :
            throw new ValidationError('Enter a valid email address.', 'invalid');
        endif;
    }
}