<?php
/**
 * This file is part of the ci4 package.
 *
 * (c) Eddilbert Macharia (http://eddmash.com)<edd.cowan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eddmash\PowerOrm\Form\Fields;

use Eddmash\PowerOrm\BaseOrm;
use Eddmash\PowerOrm\Exception\ValidationError;
use Eddmash\PowerOrm\Form\Widgets\DateInput;

//todo handle formats input by use for datefield, timefield, splitdatefield
class DateField extends Field
{
    public $defaultErrorMessages = ['invalid' => 'Enter a valid date.'];

    public function getWidget()
    {
        return DateInput::instance();
    }

    public function toPhp($value)
    {
        if (empty($value)) :
            return;
        elseif ($value instanceof \DateTime) :
            return $value;
        elseif (is_string($value)):

            //todo accept more than one formsts
            $formats = BaseOrm::getInstance()->dateFormats;
            foreach ($formats as $format) :
                if ($date = \DateTime::createFromFormat($format, $value)):
                    return $date;
                endif;
            endforeach;

        endif;
        throw new ValidationError($this->errorMessages['invalid'], 'invalid');
    }
}
