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

class CsrfField extends MultiValueField
{
    public function __construct(array $attrs = [])
    {
        $slimGuard = CsrfManager::getGuard();
        $csrfNameKey = $slimGuard->getTokenNameKey();
        $csrfValueKey = $slimGuard->getTokenValueKey();
        $keyPair = $slimGuard->generateToken();

        $fields=[
            $csrfNameKey => CharField::instance(['initial' => $keyPair[$csrfNameKey]]),
            $csrfValueKey => CharField::instance(['initial' => $keyPair[$csrfValueKey]]),
        ];
        $attrs['fields'] = $fields;
        parent::__construct($attrs);
    }

    /**{@inheritdoc}*/
    public function compress($cleanData)
    {
        return $cleanData;
    }

    /**
     * @inheritDoc
     */
    public function validate($value)
    {
        // validate only if we have a csrf enabled.
        if ($this->form->csrfIsEnabled()) :
            $slimGuard = CsrfManager::getGuard();
            dump($value[$slimGuard->getTokenNameKey()]);
            dump($value[$slimGuard->getTokenValueKey()]);
            if (!$slimGuard->validateToken($value[$slimGuard->getTokenNameKey()], $value[$slimGuard->getTokenValueKey()])) :
                throw new ValidationError("Csrf validation failed");
            endif;
        endif;
    }
}