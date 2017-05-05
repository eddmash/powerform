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

use Eddmash\PowerOrm\Exception\ValidationError;
use Eddmash\PowerOrm\Form\Widgets\HiddenInput;

/**
 * Creates a Csrf input.
 *
 * @package Eddmash\PowerOrm\Form\Widgets
 * @author: Eddilbert Macharia (http://eddmash.com)<edd.cowan@gmail.com>
 */
class CsrfField extends CharField
{

    public function __construct(array $attrs = [])
    {
        parent::__construct($attrs);


    }

    /**
     * @inheritDoc
     */
    public function validate($value)
    {
        // validate only if we have a csrf guard to use
        if ($this->form->getCsrfGuard()) :
            if (!$this->form->getCsrfGuard()->validateToken($this->name, $value)) :
                throw new ValidationError("Csrf validation failed");
            endif;
        endif;
    }



//    public function getWidget()
//    {
//        return HiddenInput::instance();
//    }
}