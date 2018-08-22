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
use Eddmash\PowerOrm\Form\CsrfManager;
use Eddmash\PowerOrm\Form\Widgets\HiddenInput;

class CsrfField extends MultiValueField
{
    public function __construct(array $attrs = [])
    {
        $slimGuard = CsrfManager::getGuard();
        $csrfNameKey = $slimGuard->getTokenNameKey();
        $csrfValueKey = $slimGuard->getTokenValueKey();
        $keyPair = $slimGuard->generateToken();

        $fields = [
            $csrfNameKey => CharField::instance(['initial' => $keyPair[$csrfNameKey],
                'widget' => HiddenInput::instance()]),
            $csrfValueKey => CharField::instance(['initial' => $keyPair[$csrfValueKey],
                'widget' => HiddenInput::instance()]),
        ];
        $attrs['fields'] = $fields;
        parent::__construct($attrs);
    }

    /**{@inheritdoc} */
    public function value()
    {
        // with csrf we pass new values for each request so we dont need what the form passed in.
        return $this->prepareValue($this->getInitial());
    }

    /**{@inheritdoc} */
    public function compress($cleanData)
    {
        return $cleanData;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value)
    {
        // validate only if we have a csrf enabled.
        if ($this->form->csrfIsEnabled()) :
            $slimGuard = CsrfManager::getGuard();
            dump($value[$slimGuard->getTokenNameKey()]);
            dump($value[$slimGuard->getTokenValueKey()]);
            if (!$slimGuard->validateToken(
                $value[$slimGuard->getTokenNameKey()],
                $value[$slimGuard->getTokenValueKey()]
            )
            ) :
                throw new ValidationError('Csrf validation failed');
            endif;
        endif;
    }
}
