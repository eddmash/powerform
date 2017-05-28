<?php
/**
 * This file is part of the ci4 package.
 *
 * (c) Eddilbert Macharia (http://eddmash.com)<edd.cowan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eddmash\PowerOrm\Form\Helpers;

use Eddmash\PowerOrm\BaseOrm;

class Tools
{
    public static function getDateFormats()
    {
        return BaseOrm::getInstance()->dateFormats;
    }
}
