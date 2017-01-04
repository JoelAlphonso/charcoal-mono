<?php

namespace Charcoal\Admin\Action\Widget;

use \Exception;
use \RuntimeException;
use \InvalidArgumentException;

// From PSR-7
use \Psr\Http\Message\RequestInterface;
use \Psr\Http\Message\ResponseInterface;

// From Pimple
use \Pimple\Container;

// From 'charcoal-factory'
use \Charcoal\Factory\FactoryInterface;

// From 'charcoal-view'
use \Charcoal\View\ViewInterface;

// From 'charcoal-admin'
use \Charcoal\Admin\AdminAction;

/**
 *
 */
class LoadAction extends AdminAction
{
    /**
     * The widget's current ID.
     *
     * @var string
     */
    protected $widgetId;

    /**
     * The widget's current type.
     *
     * @var string
     */
    protected $widgetType;

    /**
     * The widget's renderered view.
     *
     * @var string
     */
    protected $widgetHtml;

    /**
     * Store the view renderer.
     *
     * @var ViewInterface
     */
    protected $widgetView;

    /**
     * Store the widget factory.
     *
     * @var FactoryInterface
     */
    protected $widgetFactory;

    /**
     * @param  Container $container A dependencies container instance.
     * @return void
     */
    public function setDependencies(Container $container)
    {
        parent::setdependencies($container);

        $this->setWidgetFactory($container['widget/factory']);
        $this->setWidgetView($container['view']);
    }

    /**
     * Execute the endpoint.
     *
     * @param  RequestInterface  $request  A PSR-7 compatible Request instance.
     * @param  ResponseInterface $response A PSR-7 compatible Response instance.
     * @return ResponseInterface
     */
    public function run(RequestInterface $request, ResponseInterface $response)
    {
        $widgetType    = $request->getParam('widget_type');
        $widgetOptions = $request->getParam('widget_options');

        if (!$widgetType) {
            $this->setSuccess(false);
            return $response->withStatus(400);
        }

        try {
            $widget = $this->widgetFactory()->create($widgetType);
            $widget->setView($this->widgetView());

            if (is_array($widgetOptions)) {
                $widget->setData($widgetOptions);
            }

            $widgetHtml = $widget->renderTemplate($widgetType);
            $widgetId   = $widget->widgetId();

            $this->setWidgetHtml($widgetHtml);
            $this->setWidgetId($widgetId);

            $this->setSuccess(true);
            return $response;
        } catch (Exception $e) {
            $message = $e->getMessage();

            $this->addFeedback('error', 'An error occured reloading the widget');

            if ($message) {
                $this->addFeedback('error', $message);
            }

            $this->setSuccess(false);
            return $response->withStatus(500);
        }
    }

    /**
     * Set the widget's ID.
     *
     * @param  string $id The widget ID.
     * @throws InvalidArgumentException If the widget ID argument is not a string.
     * @return LoadAction Chainable
     */
    public function setWidgetId($id)
    {
        if (!is_string($id)) {
            throw new InvalidArgumentException(
                'Widget ID must be a string'
            );
        }

        $this->widgetId = $id;

        return $this;
    }

    /**
     * Retrieve the widget's ID.
     *
     * @return string
     */
    public function widgetId()
    {
        return $this->widgetId;
    }

    /**
     * Set the widget's type.
     *
     * @param  string $type The widget type.
     * @throws InvalidArgumentException If the widget type argument is not a string.
     * @return LoadAction Chainable
     */
    public function setWidgetType($type)
    {
        if (!is_string($type)) {
            throw new InvalidArgumentException(
                'Widget Type must be a string'
            );
        }

        $this->widgetType = $type;

        return $this;
    }

    /**
     * Retrieve the widget's type.
     *
     * @return string
     */
    public function widgetType()
    {
        return $this->widgetType;
    }

    /**
     * Set the widget's rendered view.
     *
     * @param string $html The widget HTML.
     * @throws InvalidArgumentException If the widget HTML is not a string.
     * @return LoadAction Chainable
     */
    public function setWidgetHtml($html)
    {
        if (!is_string($html)) {
            throw new InvalidArgumentException(
                'Widget HTML must be a string'
            );
        }

        $this->widgetHtml = $html;

        return $this;
    }

    /**
     * Retrieve the widget's rendered view.
     *
     * @return string
     */
    public function widgetHtml()
    {
        return $this->widgetHtml;
    }

    /**
     * Set the widget renderer.
     *
     * @param  ViewInterface $view The view renderer to create widgets.
     * @return self
     */
    protected function setWidgetView(ViewInterface $view)
    {
        $this->widgetView = $view;

        return $this;
    }

    /**
     * Retrieve the widget renderer.
     *
     * @throws RuntimeException If the widget renderer was not previously set.
     * @return ViewInterface
     */
    protected function widgetView()
    {
        if (!isset($this->widgetView)) {
            throw new RuntimeException('Widget Renderer is not defined');
        }

        return $this->widgetView;
    }

    /**
     * Set the widget factory.
     *
     * @param  FactoryInterface $factory The factory to create widgets.
     * @return self
     */
    protected function setWidgetFactory(FactoryInterface $factory)
    {
        $this->widgetFactory = $factory;

        return $this;
    }

    /**
     * Retrieve the widget factory.
     *
     * @throws RuntimeException If the widget factory was not previously set.
     * @return FactoryInterface
     */
    protected function widgetFactory()
    {
        if (!isset($this->widgetFactory)) {
            throw new RuntimeException('Widget Factory is not defined');
        }

        return $this->widgetFactory;
    }

    /**
     * @return string
     */
    public function results()
    {
        return [
            'success'       => $this->success(),
            'widget_html'   => $this->widgetHtml(),
            'widget_id'     => $this->widgetId(),
            'feedbacks'     => $this->feedbacks()
        ];
    }
}
