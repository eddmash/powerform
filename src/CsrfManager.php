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
    public static $guards;

    /**
     * @inheritDoc
     */
    private function __construct()
    {
        if(!isset($_SESSION)):
            session_start();
        endif;
        static::$guards =  new Guard();
    }


    /**
     * @return static
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public static function instance()
    {
        if(!static::$instance):
            static::$instance = new static();
        endif;

        return static::$instance;
    }

    private function guard()
    {
        return static::$guards;
    }

    public static function getGuard()
    {
        return static::instance()->guard();
    }

}