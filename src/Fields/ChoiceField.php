<?php
/**
 * Created by http://eddmash.com
 * User: eddmash
 * Date: 7/16/16
 * Time: 2:08 PM.
 */

namespace Eddmash\PowerOrm\Form\Fields;

use Eddmash\PowerOrm\Form\Widgets\MultipleCheckboxes;
use Eddmash\PowerOrm\Form\Widgets\Select;
use Eddmash\PowerOrm\Form\Widgets\SelectMultiple;
use Eddmash\PowerOrm\Helpers\ArrayHelper;

/**
 * Creates a :
 *      Default widget: Select
 *      Empty value: '' (an empty string)
 * Takes one extra required argument:
 *      choices
 *          - Takes an associative array of value=>label e.g. ['f'=>'female'] or with grouping
 *              $MEDIA_CHOICES = [
 *                  'Audio'=>[
 *                      'vinyl'=>'Vinyl',
 *                      'cd'=> 'CD',
 *                  ],
 *                  'Video'=> [
 *                      'vhs'=> 'VHS Tape',
 *                      'dvd'=> 'DVD',
 *                  ],
 *                  'unknown'=> 'Unknown',
 *              ];.
 *
 * Class ChoiceField
 *
 * @since 1.1.0
 *
 * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
 */
class ChoiceField extends Field
{
    protected $choices = [];

    public $defaultErrorMessages = [
        'invalid_choice' => 'Select a valid choice. %(value)s is not one of the available choices.',
    ];

    public function __construct($opts = [])
    {
        $choices = ArrayHelper::pop($opts, 'choices', []);
        parent::__construct($opts);
        $this->setChoices($choices);
    }

    /**
     * {@inheritdoc}
     */
    public function getHtmlName()
    {
        if ($this->widget instanceof SelectMultiple || $this->widget instanceof MultipleCheckboxes):
            return sprintf('%s[]', $this->htmlName);

        endif;

        return parent::getHtmlName();
    }

    public function getWidget()
    {
        return Select::instance();
    }

    /**
     * @return array
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * @param array $choices
     */
    public function setChoices($choices)
    {
        if (is_callable($choices)) :
            $choices = call_user_func($choices);
        endif;

        $this->widget->choices = $this->choices = $choices;
    }
}
