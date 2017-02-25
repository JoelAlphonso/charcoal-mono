<?php

namespace Charcoal\App\Handler;

use InvalidArgumentException;

// Dependency from 'charcoal-app'
use Charcoal\App\App;

// Dependency from 'charcoal-config'
use Charcoal\Config\AbstractConfig;

/**
 *
 */
class HandlerConfig extends AbstractConfig
{

    /**
     * The template ident (to load).
     * @var string $template
     */
    private $template;

    /**
     * The view engine ident to use.
     * Ex: "mustache", ""
     * @var string $engine
     */
    private $engine;

    /**
     * Additional template data.
     * @var array $templateData
     */
    private $templateData = [];

    /**
     * Response controller classname
     *
     * Should be the class-ident of a template controller.
     *
     * @var string
     */
    private $controller;

    /**
     * Enable route-level caching for this template.
     * @var boolean $cache
     */
    private $cache = false;

    /**
     * If using cache, the time-to-live, in seconds, of the cache. (0 = no limit).
     * @var integer $cache_ttl
     */
    private $cache_ttl = 0;

    /**
     * @param string|null $template The template identifier.
     * @throws InvalidArgumentException If the tempalte parameter is not null or not a string.
     * @return HandlerConfig Chainable
     */
    public function setTemplate($template)
    {
        if ($template === null) {
            $this->template = null;
            return $this;
        }
        if (!is_string($template)) {
            throw new InvalidArgumentException(
                'Template must be a string (the template ident)'
            );
        }
        $this->template = $template;
        return $this;
    }

    /**
     * @return string
     */
    public function template()
    {
        if ($this->template === null) {
            return 'charcoal/app/handler/default';
        }
        return $this->template;
    }

    /**
     * Set handler view controller classname
     *
     * @param string $controller Handler controller name.
     * @throws InvalidArgumentException If the handler view controller is not a string.
     * @return RouteConfig Chainable
     */
    public function setController($controller)
    {
        if (!is_string($controller)) {
            throw new InvalidArgumentException(
                'Handler view controller must be a string.'
            );
        }

        $this->controller = $controller;

        return $this;
    }

    /**
     * Get the view controller classname
     *
     * @return string
     */
    public function controller()
    {
        if (!isset($this->controller)) {
            return $this->defaultController();
        }

        return $this->controller;
    }

    /**
     * @return string
     */
    public function defaultController()
    {
        $config = App::instance()->config();

        if ($config->has('view.default_controller')) {
            return $config->get('view.default_controller');
        }
    }

    /**
     * @param string|null $engine The engine identifier (mustache, php, or mustache-php).
     * @throws InvalidArgumentException If the engine is not null or not a string.
     * @return HandlerConfig Chainable
     */
    public function setEngine($engine)
    {
        if ($engine === null) {
            $this->engine = null;
            return $this;
        }
        if (!is_string($engine)) {
            throw new InvalidArgumentException(
                'Engine must be a string (the engine ident)'
            );
        }
        $this->engine = $engine;
        return $this;
    }

    /**
     * @return string
     */
    public function engine()
    {
        if ($this->engine === null) {
            return $this->defaultEngine();
        }
        return $this->engine;
    }

    /**
     * @return string
     */
    public function defaultEngine()
    {
        $config = App::instance()->config();

        if ($config->has('view.default_engine')) {
            return $config->get('view.default_engine');
        } else {
            return 'mustache';
        }
    }

    /**
     * Set the template data for the view.
     *
     * @param array $templateData The route template data.
     * @return HandlerConfig Chainable
     */
    public function setTemplateData(array $templateData)
    {
        if (!isset($this->templateData)) {
            $this->templateData = [];
        }

        $this->templateData = array_merge($this->templateData, $templateData);

        return $this;
    }

    /**
     * Get the template data for the view.
     *
     * @return array
     */
    public function templateData()
    {
        return $this->templateData;
    }

    /**
     * @param boolean $cache The cache enabled flag.
     * @return HandlerConfig Chainable
     */
    public function setCache($cache)
    {
        $this->cache = !!$cache;
        return $this;
    }

    /**
     * @return boolean
     */
    public function cache()
    {
        return $this->cache;
    }

    /**
     * @param integer $ttl The cache Time-To-Live, in seconds.
     * @return HandlerConfig Chainable
     */
    public function setCacheTtl($ttl)
    {
        $this->cache_ttl = (integer)$ttl;
        return $this;
    }

    /**
     * @return integer
     */
    public function cacheTtl()
    {
        return $this->cache_ttl;
    }
}
