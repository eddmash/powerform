<?php

/**
 * This file is part of the ci4 package.
 *
 * (c) Eddilbert Macharia (http://eddmash.com)<edd.cowan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eddmash\PowerOrm\Form;

use Slim\Csrf\Guard;

class CsrfManager
{
    private static $instance;

    private function __construct()
    {
        // start session just incase.
        if (!isset($_SESSION)):
            session_start();
        endif;
    }

    /**
     * @return static
     *
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public static function instance()
    {
        if (!static::$instance):
            static::$instance = new static();
        endif;

        return static::$instance;
    }

    /**
     * @return Guard
     * @author: Eddilbert Macharia (http://eddmash.com)<edd.cowan@gmail.com>
     */
    private function guard()
    {
        $slimGuard = new Guard();
        $slimGuard->validateStorage();

        return $slimGuard;
    }

    /**
     * @return Guard
     * @author: Eddilbert Macharia (http://eddmash.com)<edd.cowan@gmail.com>
     */
    public static function getGuard()
    {
        return static::instance()->guard();
    }

}
