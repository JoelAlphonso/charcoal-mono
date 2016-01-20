<?php

namespace Charcoal\Admin;

// Dependencies from `PHP`
use \InvalidArgumentException;

// From `charcoal-core`
use \Charcoal\Translation\TranslationString;

// From `charcoal-base`
use \Charcoal\App\Template\AbstractWidget;
use \Charcoal\Widget\WidgetView;

/**
 * The base Widget for the `admin` module.
 */
class AdminWidget extends AbstractWidget
{
    /**
     * @var string $widgetId
     */
    public $widgetId;

    /**
     * @var string $type
     */
    private $type;

    /**
     * @var string $ident
     */
    private $ident = '';

    /**
     * @var mixed $label
     */
    private $label;

    /**
     * @var string $lang
     */
    private $lang;

    /**
     * @var bool $showLabel
     */
    private $showLabel;

    /**
     * @var bool $showActions
     */
    private $showActions;


    /**
     * @param string $widgetId The widget identifier.
     * @return AdminWidget Chainable
     */
    public function setWidgetId($widgetId)
    {
        $this->widgetId = $widgetId;
        return $this;
    }

    /**
     * @return string
     */
    public function widgetId()
    {
        if (!$this->widgetId) {
            $this->widgetId = 'widget_'.uniqid();
        }
        return $this->widgetId;
    }

    /**
     * @param string $type
     * @throws InvalidArgumentException
     * @return AdminWidget Chainable
     */
    public function setType($type)
    {
        if (!is_string($type)) {
            throw new InvalidArgumentException(
                'Template ident must be a string'
            );
        }
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function type()
    {
        return $this->type;
    }

    /**
     * @param string $ident
     * @throws InvalidArgumentException if the ident is not a string
     * @return AdminWidget (Chainable)
     */
    public function setIdent($ident)
    {
        if (!is_string($ident)) {
            throw new InvalidArgumentException(
                __CLASS__.'::'.__FUNCTION__.'() - Ident must be a string.'
            );
        }
        $this->ident = $ident;
        return $this;
    }

    /**
     * @return string
     */
    public function ident()
    {
        return $this->ident;
    }

    /**
     * @param mixed $label
     * @return AdminWidget Chainable
     */
    public function setLabel($label)
    {
        $this->label = new TranslationString($label);
        return $this;
    }

    /**
     * @return string
     */
    public function label()
    {
        if ($this->label === null) {
            // Generate label from ident
            $label = ucwords(str_replace(['_', '.', '/'], ' ', $this->ident()));
            $this->label = new TranslationString($label);
        }
        return $this->label;
    }

    public function actions()
    {
        return [];
    }

    /**
     * @param boolean $show
     * @return AdminWidget Chainable
     */
    public function setShowActions($show)
    {
        $this->showActions = !!$show;
        return $this;
    }

    /**
     * @return boolean
     */
    public function showActions()
    {
        if ($this->showActions !== false) {
            return (count($this->actions()) > 0);
        } else {
            return false;
        }
    }

    /**
     * @param boolean $show
     * @return AdminWidget Chainable
     */
    public function setShowLabel($show)
    {
        $this->showLabel = !!$show;
        return $this;
    }

    /**
     * @return boolean
     */
    public function showLabel()
    {
        if ($this->showLabel !== false) {
            return ((string)$this->label() == '');
        } else {
            return false;
        }
    }

    /**
     * @param mixed $template Unused
     * @return string
     */
    public function render($template = null)
    {
        unset($template);
        $view = new WidgetView();
        $view->set_context($this);
        $content = $view->renderTemplate($this->ident());
        return $content;
    }
}
