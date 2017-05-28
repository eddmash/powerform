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

use Eddmash\PowerOrm\BaseOrm;

class BaseValidator
{
    public function __construct($kwargs = [])
    {
        BaseOrm::configure($this, $kwargs);
    }

    public static function instance($kwargs = [])
    {
        return new static($kwargs);
    }
}
