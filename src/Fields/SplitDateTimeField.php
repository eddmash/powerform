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

class SplitDateTimeField extends MultiValueField
{
    public function __construct(array $attrs = [])
    {
        $fields = [
            DateField::instance(),
            TimeField::instance(),
        ];
        $attrs['fields'] = $fields;
        parent::__construct($attrs);
    }

    /**
     * {@inheritdoc}
     */
    public function compress($cleanData)
    {
        $dateFormat = BaseOrm::getInstance()->dateFormats[0];
        $timeFormat = BaseOrm::getInstance()->timeFormats[0];
        $date = $cleanData[0];
        $time = $cleanData[1];
        if (empty($date) || empty($time)):
            return;
        endif;
        $datetime = sprintf('%s %s', $date->format($dateFormat), $time->format($timeFormat));

        return new \DateTime($datetime);
    }

}
