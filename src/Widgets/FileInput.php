<?php

/**
 * This file is part of the ci4 package.
 *
 * (c) Eddilbert Macharia (http://eddmash.com)<edd.cowan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eddmash\PowerOrm\Form\Widgets;

use Eddmash\PowerOrm\Helpers\ArrayHelper;

class FileInput extends Input
{
    public $inputType = 'file';
    public $needsMultipartForm = true;

    /**
     * {@inheritdoc}
     */
    public function valueFromDataCollection($data, $file, $name)
    {
        return ArrayHelper::getValue($file, $name);
    }

}
