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

use Eddmash\PowerOrm\Exception\ValueError;
use Eddmash\PowerOrm\Helpers\ArrayHelper;
use Eddmash\PowerOrm\Helpers\Tools;

class ErrorDict extends Collection
{
    /**
     * {@inheritdoc}
     */
    public function __construct(array $data = [])
    {
        $this->ensureAssociative($data);
        parent::__construct($data);
    }

    /**
     * Ensures we are working with an associative array.
     *
     * @param $data
     *
     * @throws ValueError
     *
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    private function ensureAssociative($data)
    {
        if (!empty($data) && (!ArrayHelper::isAssociative($data) || !is_array($data))):
            throw new ValueError(sprintf(" '%s' expexts and associative array", static::class));
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        try {
            return $this->asUl();
        } catch (\Exception $exception) {
            Tools::convertExceptionToError($exception);

            return '';
        }
    }

    private function asUl()
    {
        $errors = '';
        foreach ($this->data as $datum) {
            foreach ($datum as $name => $item) {
                $errors .= sprintf('<li>%s %s</li>', $name, (string) $item);
            }
        }

        return sprintf('<ul class="errorlist">%s</ul>', $errors);
    }

    public function get($name, $default = null)
    {
        return ErrorList::instance($this->data[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function add($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function asData()
    {
        $err = [];
        foreach ($this->data as $name => $datum) {

            $err[$name] = $datum;
        }

        return $err;
    }

}
