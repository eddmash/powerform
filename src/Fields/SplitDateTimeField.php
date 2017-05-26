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



class SplitDateTimeField extends MultiValueField
{
    public function __construct(array $attrs = [])
    {
        $fields=[
            DateField::instance(),
            TimeField::instance(),
        ];
        $attrs['fields'] = $fields;
        parent::__construct($attrs);
    }

    /**
     * @inheritDoc
     */
    public function compress($cleanData)
    {
        dump($cleanData);
    }


}