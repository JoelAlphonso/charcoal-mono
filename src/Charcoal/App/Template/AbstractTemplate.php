<?php

namespace Charcoal\App\Template;

// PSR-3 (logger) dependencies
use \Psr\Log\LoggerAwareInterface;
use \Psr\Log\LoggerAwareTrait;

// Dependencies from `Pimple`
use \Pimple\Container;

// Module `charcoal-view` dependencies
use \Charcoal\View\GenericView;
use \Charcoal\View\ViewableInterface;
use \Charcoal\View\ViewableTrait;

// Intra-module (`charcoal-app`) dependencies
use \Charcoal\App\AppAwareInterface;
use \Charcoal\App\AppAwareTrait;
use \Charcoal\App\AppInterface;
use \Charcoal\App\Template\TemplateInterface;

/**
 *
 */
abstract class AbstractTemplate implements
    AppAwareInterface,
    LoggerAwareInterface,
    TemplateInterface,
    ViewableInterface
{
    use AppAwareTrait;
    use ViewableTrait;
    use LoggerAwareTrait;

    /**
     * @param array $data The dependencies (app and logger).
     */
    public function __construct(array $data = null)
    {
        $this->setLogger($data['logger']);

        // @todo: Remove this dependency.
        if (isset($data['app'])) {
            $this->setApp($data['app']);
        }
    }

    /**
     * Give an opportunity to children classes to inject dependencies from a Pimple Container.
     *
     * Does nothing by default, reimplement in children classes.
     *
     * The `$container` DI-container (from `Pimple`) should not be saved or passed around, only to be used to
     * inject dependencies (typically via setters).
     *
     * @param Container $container A dependencies container instance.
     * @return void
     */
    public function setDependencies(Container $container)
    {
        // Nothing to do.
    }

    /**
     * @param array $data The data array (as [key=>value] pair) to set.
     * @return AbtractTemplate Chainable
     */
    public function setData(array $data)
    {
        foreach ($data as $prop => $val) {
            if ($val === null) {
                continue;
            }

            $func = [$this, $this->setter($prop)];
            if (is_callable($func)) {
                call_user_func($func, $val);
            } else {
                $this->{$prop} = $val;
            }
        }

        // Chainable
        return $this;
    }

    /**
     * The default Template View is a simple GenericView.
     *
     * @param array $data The optional view data.
     * @return \Charcoal\View\ViewInterface
     */
    public function createView(array $data = null)
    {
        $view = new GenericView([
            'logger' => $this->logger
        ]);
        if ($data !== null) {
            $view->setData($data);
        }
        return $view;
    }

    /**
     * Allow an object to define how the key getter are called.
     *
     * @param string $key The key to get the getter from.
     * @return string The getter method name, for a given key.
     */
    protected function getter($key)
    {
        $getter = $key;
        return $this->camelize($getter);
    }

    /**
     * Allow an object to define how the key setter are called.
     *
     * @param string $key The key to get the setter from.
     * @return string The setter method name, for a given key.
     */
    protected function setter($key)
    {
        $setter = 'set_'.$key;
        return $this->camelize($setter);

    }

    /**
     * Transform a snake_case string to camelCase.
     *
     * @param string $str The snake_case string to camelize.
     * @return string The camelCase string.
     */
    private function camelize($str)
    {
        return lcfirst(implode('', array_map('ucfirst', explode('_', $str))));
    }
}
