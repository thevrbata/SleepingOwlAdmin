<?php

namespace SleepingOwl\Admin\Form\Element;

use Request;
use Illuminate\Http\UploadedFile;

class Upload extends NamedFormElement
{
    /**
     * @var string
     */
    protected $view = 'form.element.upload';

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'value' => $this->getModel()->getAttribute($this->getAttribute()),
        ] + parent::toArray();
    }

    /**
     * @return UploadedFile|null
     */
    public function getValue()
    {
        return Request::file($this->getPath());
    }

    public function save()
    {
        $value = $this->getValue();

        if (Request::input($this->getPath().'_remove')) {
            $this->setValue($this->getModel(), $this->getAttribute(), null);
        } elseif (! is_null($value)) {
            $this->setValue($this->getModel(), $this->getAttribute(), $value);
        }
    }
}
