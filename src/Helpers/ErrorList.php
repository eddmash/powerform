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

use Eddmash\PowerOrm\Exception\NotImplemented;
use Eddmash\PowerOrm\Helpers\ArrayHelper;
use Eddmash\PowerOrm\Helpers\Tools;

/**
 * A collection of errors that knows how to display itself in various formats.
 *
 * @since 1.1.0
 *
 * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
 */
class ErrorList extends Collection
{
    /**
     * @var \Exception[]
     */
    protected $data = [];
    private $cssClass = 'errorlist';

    /**
     * {@inheritdoc}
     */
    public function __construct(array $data, $cssClass = null)
    {
        parent::__construct($data);
        if (!is_null($cssClass)):
            $this->cssClass = sprintf('%s %s', $this->cssClass, $cssClass);
        endif;
    }

    public static function instance($data = [], $cssClass = null)
    {
        return new static($data, $cssClass);
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

    /**
     * Returns errors a html list.
     *
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    private function asUl()
    {
        $errors = '';

        foreach ($this->data as $name=>$error) :
            $errors .= sprintf('<li>%s</li>', (string) $error);
        endforeach;

        return sprintf('<ul class="%s">%s</ul>', $this->cssClass, $errors);
    }

    public function add($value)
    {
        $this->data[] = $value;
    }

    public function get($name, $default = null)
    {
        return ArrayHelper::getValue($this->data, $name, $default);
    }

    /**
     * Adds more error to the list.
     *
     * @param array $data
     *
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public function extend(array $data)
    {
        $this->data = array_merge($this->data, $data);
    }

    /**
     * @return array
     *
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public function asData()
    {
        throw new NotImplemented();
    }
}
