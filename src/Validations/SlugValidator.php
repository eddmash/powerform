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

class SlugValidator extends BaseValidator
{
    /**
     * {@inheritdoc}
     */
    public function __invoke($value)
    {
        if (!Validator::slug()->validate($value)) :
            throw new ValidationError(
                "Enter a valid 'slug' consisting of letters, numbers, underscores or hyphens.",
                'invalid'
            );
        endif;
    }
}
