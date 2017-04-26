<?php

namespace Eddmash\PowerOrm\Form\Validations;

use Eddmash\PowerOrm\Form\Exception\ValidationError;
use Respect\Validation\Validator as RespectValidator;

/**
 * This file is part of the ci4 package.
 *
 * (c) Eddilbert Macharia (http://eddmash.com)<edd.cowan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class MaxValueValidator extends BaseValidator
{
    public $min;
    public $inclusive = false;

    /**
     * @inheritDoc
     */
    function __invoke($value)
    {

        if (!RespectValidator::max($this->min, $this->inclusive)->validate($value)):
            throw new ValidationError(
                sprintf('Ensure this value is less than or equal to %s.', $this->min),
                'minValue'
            );
        endif;
    }

}