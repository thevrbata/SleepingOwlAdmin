<?php

namespace SleepingOwl\Admin\Form\Element;

use SleepingOwl\Admin\Exceptions\WysiwygException;
use SleepingOwl\Admin\Contracts\Wysiwyg\WysiwygEditorInterface;

class Wysiwyg extends NamedFormElement
{
    /**
     * @var string|null
     */
    protected $editor;

    /**
     * @var bool|null
     */
    protected $collapsed;

    /**
     * @var string|null
     */
    protected $filteredFieldKey;

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @var bool
     */
    protected $filterValue = true;

    /**
     * @var string
     */
    protected $view = 'form.element.wysiwyg';

    /**
     * Wysiwyg constructor.
     * @param $path
     * @param null $label
     * @param null $editor
     * @throws \SleepingOwl\Admin\Exceptions\Form\FormElementException
     */
    public function __construct($path, $label = null, $editor = null)
    {
        parent::__construct($path, $label);

        if (is_null($editor)) {
            $editor = app('sleeping_owl.wysiwyg')->getDefaultEditorId();
        }

        $this->setEditor($editor);
    }

    /**
     * @throws WysiwygException
     */
    public function initialize()
    {
        /** @var WysiwygEditorInterface $editor */
        $editor = app('sleeping_owl.wysiwyg')->getEditor($this->getEditor());

        if (is_null($editor)) {
            throw new WysiwygException("Wysiwyg editor [{$this->getEditor()}] is not defined.");
        }

        app('sleeping_owl.wysiwyg')->loadEditor($this->getEditor());

        $config = $editor->getConfig();
        $config->set($this->parameters);

        if (! $this->hasHtmlAttribute('id')) {
            $this->setHtmlAttribute('id', $this->getName());
        }

        $this->parameters = (array) $config->all();

        $params = collect($this->parameters);

        if (! $params->has('uploadUrl')) {
            $this->parameters['uploadUrl'] = route('admin.ckeditor.upload', ['_token' => csrf_token()]);
        }

        if (! $params->has('filebrowserUploadUrl')) {
            $this->parameters['filebrowserUploadUrl'] = route('admin.ckeditor.upload', ['_token' => csrf_token()]);
        }
    }

    /**
     * @return $this
     */
    public function disableFilter()
    {
        $this->filterValue = false;

        return $this;
    }

    /**
     * @return bool
     */
    public function canFilterValue()
    {
        return $this->filterValue;
    }

    /**
     * @return null|string
     */
    public function getEditor()
    {
        return $this->editor;
    }

    /**
     * @return null|string
     */
    public function getCollapsed()
    {
        return $this->collapsed;
    }

    /**
     * @param null|string $editor
     *
     * @return $this
     */
    public function setEditor($editor)
    {
        $this->editor = $editor;

        return $this;
    }

    /**
     * @param null|bool $collapsed
     *
     * @return $this
     */
    public function setCollapsed($collapsed)
    {
        $this->collapsed = $collapsed;

        return $this;
    }

    /**
     * @param int|null $height
     *
     * @return $this
     */
    public function setHeight($height)
    {
        $this->parameters['height'] = (int) $height;

        return $this;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     *
     * @return $this
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @param string $field
     *
     * @return $this
     */
    public function setFilteredValueToField($field)
    {
        $this->filteredFieldKey = $field;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return ['attributes' => $this->getHtmlAttributes()] + parent::toArray() + [
                'parameters' => json_encode($this->getParameters()),
                'editor' => $this->getEditor(),
                'collapsed' => $this->getCollapsed(),
            ];
    }

    /**
     * @param mixed $value
     *
     * @return void
     */
    public function setModelAttribute($value)
    {
        if (! empty($this->filteredFieldKey)) {
            parent::setModelAttribute($value);

            $this->setModelAttributeKey($this->filteredFieldKey);
            parent::setModelAttribute(
                $this->filterValue($value)
            );
        } else {
            parent::setModelAttribute(
                $this->filterValue($value)
            );
        }
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function filterValue($value)
    {
        if ($this->canFilterValue()) {
            return app('sleeping_owl.wysiwyg')->applyFilter($this->getEditor(), $value);
        }

        return $value;
    }
}
