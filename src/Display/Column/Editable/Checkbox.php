<?php

namespace SleepingOwl\Admin\Display\Column\Editable;

use Illuminate\Http\Request;
use SleepingOwl\Admin\Form\FormDefault;
use SleepingOwl\Admin\Contracts\Display\ColumnEditableInterface;

class Checkbox extends EditableColumn implements ColumnEditableInterface
{
    /**
     * @var string
     */
    protected $view = 'column.editable.checkbox';

    /**
     * @var bool
     */
    protected $orderable = false;

    /**
     * @var bool
     */
    protected $isSearchable = false;

    /**
     * @var null|string
     */
    protected $checkedLabel;

    /**
     * @var null|string
     */
    protected $uncheckedLabel;

    /**
     * @var string
     */
    protected $width = '50px';

    /**
     * Checkbox constructor.
     *
     * @param string $name
     * @param string|null $checkedLabel
     * @param string|null $uncheckedLabel
     * @param string|null $columnLabel
     */
    public function __construct($name, $checkedLabel = null, $uncheckedLabel = null, $columnLabel = null)
    {
        parent::__construct($name, $columnLabel);

        $this->checkedLabel = $checkedLabel;
        $this->uncheckedLabel = $uncheckedLabel;
    }

    public function getModifierValue()
    {
        if (is_callable($this->modifier)) {
            return call_user_func($this->modifier, $this);
        }

        if (is_null($this->modifier)) {
            return $this->getModelValue() ? $this->getCheckedLabel() : $this->getUncheckedLabel();
        }

        return $this->modifier;
    }

    /**
     * @return null|string|array
     */
    public function getCheckedLabel()
    {
        if (is_null($label = $this->checkedLabel)) {
            $label = trans('sleeping_owl::lang.editable.checkbox.checked');
        }

        return $label;
    }

    /**
     * @param null|string $label
     *
     * @return $this
     */
    public function setCheckedLabel($label)
    {
        $this->checkedLabel = $label;

        return $this;
    }

    /**
     * @return null|string|array
     */
    public function getUncheckedLabel()
    {
        if (is_null($label = $this->uncheckedLabel)) {
            $label = trans('sleeping_owl::lang.editable.checkbox.unchecked');
        }

        return $label;
    }

    /**
     * @param null|string $label
     *
     * @return $this
     */
    public function setUncheckedLabel($label)
    {
        $this->uncheckedLabel = $label;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_merge(parent::toArray(), [
            'checkedLabel' => $this->getCheckedLabel(),
            'uncheckedLabel' => $this->getUncheckedLabel(),
            'text' => $this->getModifierValue(),
        ]);
    }

    /**
     * @param Request $request
     * @throws \SleepingOwl\Admin\Exceptions\Form\FormElementException
     * @throws \SleepingOwl\Admin\Exceptions\Form\FormException
     */
    public function save(Request $request)
    {
        $form = new FormDefault([
            new \SleepingOwl\Admin\Form\Element\Checkbox(
                $this->getName()
            ),
        ]);

        $model = $this->getModel();

        $request->offsetSet($this->getName(), $request->input('value.0') ?? false);

        $form->setModelClass(get_class($model));
        $form->initialize();
        $form->setId($model->getKey());

        $form->saveForm($request);
    }
}
