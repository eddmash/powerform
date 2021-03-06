<?php
/**
 * Created by eddmash <http://eddmash.com>
 * Date: 6/23/16
 * Time: 3:55 PM.
 */

namespace Eddmash\PowerOrm\Form\Widgets;

use Eddmash\PowerOrm\BaseObject;
use Eddmash\PowerOrm\Exception\NotImplemented;

/**
 * base class for all widgets, should never initialized
 * Class Widget.
 *
 * @since 1.1.0
 *
 * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
 */
abstract class Widget extends BaseObject
{
    /**
     * Should only contain attributes that will be added to final html element. e.g class, id, e.t.c.
     *
     * @var array
     */
    public $attrs;
    public $needsMultipartForm = false;
    public $isRequired = false;

    public function __construct($attrs = [])
    {
        $this->attrs = $attrs;
    }

    public static function instance($attrs = [])
    {
        return new static($attrs);
    }

    public function buildAttrs($baseAttrs = [], $extraAttrs = [])
    {
        $finalAttrs = array_merge($this->attrs, $extraAttrs);

        if (!empty($baseAttrs)):
            $finalAttrs = array_merge($baseAttrs, $finalAttrs);
        endif;

        return $finalAttrs;
    }

    public function render($name, $value, $attrs = [])
    {
        throw new NotImplemented('subclasses of Widget must provide a render() method');
    }

    /**
     * Prepare value for use on HTML widget.
     *
     * @param $value
     *
     * @return mixed
     *
     * @since 1.1.0
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public function formatValue($value)
    {
        return $value;
    }

    /**
     * Individual widgets know how to handle data passed in to the form for use on the widget.
     *
     * @param array $data oftern will come from $_POST
     * @param array $files most often will come from $_FILES
     * @param $name
     *
     * @return mixed
     *
     * @since 1.1.0
     * @author: Eddilbert Macharia (http://eddmash.com)<edd.cowan@gmail.com>
     */
    public function valueFromDataCollection($data, $files, $name)
    {
        return (isset($data[$name])) ? $data[$name] : null;
    }

    public function isHidden()
    {
        return (property_exists($this, 'inputType')) ? 'hidden' === $this->inputType : false;
    }

    public function flatAttrs($attrs)
    {
        $strAttrs = '';
        foreach ($attrs as $key => $attr) :
            if (true === $attrs || false === $attrs):
                $strAttrs .= ' ' . $key;
            else:
                $strAttrs .= sprintf(' %s = "%s" ', $key, $attr);
            endif;
        endforeach;

        return $strAttrs;
    }

    /**Returns the HTML ID attribute of this Widget for use by a <label>,
     * given the ID of the field. Returns None if no ID is available.
     * This hook is necessary because some widgets have multiple HTML
     * elements and, thus, multiple IDs. In that case, this method should
     * return an ID value that corresponds to the first ID in the widget's
     * tags.
     * @since 1.1.0
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    public function getIdForLabel($id)
    {
        return $id;
    }
}
