<?php

/**
 * This file is part of the ci4 package.
 *
 * (c) Eddilbert Macharia (http://eddmash.com)<edd.cowan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eddmash\PowerOrm\Form\Widgets;


use Eddmash\PowerOrm\Exception\KeyError;
use Eddmash\PowerOrm\Exception\NotImplemented;
use Eddmash\PowerOrm\Helpers\ArrayHelper;

class MultiWidget extends Input
{
    /**
     * @var Input[]
     */
    private $subWidgets=[];

    /**
     * @param Widget[] $subWidgets
     */
    public function setSubWidgets($subWidgets)
    {
        $this->subWidgets = $subWidgets;
    }

    /**
     * @inheritDoc
     */
    public function render($name, $value, $attrs = [])
    {
        $finalAttrs = $this->getFinalAttrs($name, $value, $attrs);
        $type = ArrayHelper::pop($finalAttrs, 'type', null);
        ArrayHelper::pop($finalAttrs, 'value', null);
        $id = ArrayHelper::getValue($finalAttrs, 'id');

        if (!is_array($value)) :
            $value = $this->decompress($value);
        endif;

        $widgetHtml = [];

        foreach ($this->subWidgets as $index =>$subWidget) :

            if (!is_null($type)) :
                $subWidget->inputType = $type;
            endif;

            $widgetName = $this->getWidgetName($name, $index);
            try{
                $widgetValue = ArrayHelper::getValue($value, $index, ArrayHelper::STRICT);
            }catch (KeyError $keyError){
                $widgetValue = null;
            }

            if (is_null($id)) :
                $widgetAttrs = $finalAttrs;
            else:
                $widgetAttrs = $finalAttrs;
                $widgetAttrs['id']= sprintf("%s_%s", $id, $index);
            endif;

            $widgetHtml[] =$subWidget->render($widgetName, $widgetValue, $widgetAttrs);
        endforeach;
        return implode(" ", $widgetHtml);
    }

    /**
     * @inheritDoc
     */
    public function valueFromDataCollection($data, $files, $name)
    {
        $value = [];
        foreach ($this->subWidgets as $index=>$subWidget) :
            $value[$index]= parent::valueFromDataCollection($data, $files, $this->getWidgetName($name, $index));
        endforeach;
        return $value;
    }

    public function getWidgetName($name, $widgetId)
    {
        return sprintf("%s_%s", $name, $widgetId);
    }

    /**
     * Returns a list of decompressed values for the given compressed value.  The given value can be assumed to be
     * valid, but not necessarily non-empty.
     *
     * @param $value
     * @throws NotImplemented
     * @author: Eddilbert Macharia (http://eddmash.com)<edd.cowan@gmail.com>
     */
    public function decompress($value)
    {
        throw new  NotImplemented('Subclasses must implement this method.');
    }
}