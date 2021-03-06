<?php
/**
 * Created by http://eddmash.com
 * User: eddmash
 * Date: 7/16/16
 * Time: 2:09 PM.
 */

namespace Eddmash\PowerOrm\Form\Fields;

use Eddmash\PowerOrm\Exception\TypeError;
use Eddmash\PowerOrm\Exception\ValidationError;
use Eddmash\PowerOrm\Exception\ValueError;

class TypedChoiceField extends ChoiceField
{
    public function clean($value)
    {
        $value = parent::clean($value);

        return $this->_coerce($value);
    }

    public function _coerce($value)
    {
        if (empty($value)):
            return $value;
        endif;

        try {
            $value = call_user_func_array($this->coerce, [$value]);
        } catch (ValueError $e) {
        } catch (ValidationError $v) {
        } catch (TypeError $t) {
        }

        return $value;
    }
}
