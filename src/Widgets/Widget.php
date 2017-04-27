<?php
/**
 * Created by eddmash <http://eddmash.com>
 * Date: 6/23/16
 * Time: 3:55 PM.
 */

namespace Eddmash\PowerOrm\Form\Widgets;

use Eddmash\PowerOrm\Exception\NotImplemented;
use Eddmash\PowerOrm\BaseObject;

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
    public $attrs;
    public $needs_multipart_form = false;
    public $isRequired = false;

    public function __construct($attrs = [], $kwargs = [])
    {
        $this->attrs = $attrs;
    }

    public static function instance($attrs = [], $kwargs = [])
    {
        return new static($attrs, $kwargs);
    }

    public function buildAttrs($attrs = [], $kwargs = [])
    {

        $finalAttrs = array_merge($this->attrs, $kwargs);

        if (!empty($attrs)):
            $finalAttrs = array_merge($finalAttrs, $attrs);
        endif;

        return $finalAttrs;
    }

    public function render($name, $value, $attrs = [], $kwargs = [])
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
    public function prepareValue($value)
    {
        return $value;
    }

    public function valueFromDataCollection($data, $name)
    {
        return (isset($data[$name])) ? $data[$name] : null;
    }

    public function isHidden()
    {
        return (property_exists($this, 'inputType')) ? $this->inputType === 'hidden' : false;
    }

    public function flatAttrs($attrs)
    {
        $strAttrs = '';
        foreach ($attrs as $key => $attr) :
            if ($attrs === true || $attrs === false):
                $strAttrs .= ' '.$key;
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
