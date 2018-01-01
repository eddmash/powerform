<?php

namespace Eddmash\PowerOrm\Form\Validations;

use Eddmash\PowerOrm\Exception\ValidationError;
use Respect\Validation\Validator as RespectValidator;

/**
 * This file is part of the ci4 package.
 *
 * (c) Eddilbert Macharia (http://eddmash.com)<edd.cowan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class MinValueValidator extends BaseValidator
{
    public $min;
    public $inclusive = false;

    /**
     * {@inheritdoc}
     */
    public function __invoke($value)
    {
        if (!RespectValidator::max($this->min, $this->inclusive)->validate($value)):
            throw new ValidationError(
                sprintf('Ensure this value is less than or equal to %s.', $this->min),
                'minValue'
            );
        endif;
    }
}
