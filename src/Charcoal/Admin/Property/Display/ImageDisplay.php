<?php

namespace Charcoal\Admin\Property\Display;

use \Charcoal\Admin\Ui\ImageAttributesTrait;
use \Charcoal\Admin\Property\AbstractPropertyDisplay;
use Pimple\Container;

/**
 * Image Display Property
 */
class ImageDisplay extends AbstractPropertyDisplay
{
    use ImageAttributesTrait;

    /**
     * The base URI for the Charcoal application.
     *
     * @var string|\Psr\Http\Message\UriInterface
     */
    public $baseUrl;

    /**
     * Inject dependencies from a DI Container.
     *
     * @param Container $container A dependencies container instance.
     * @return void
     */
    public function setDependencies(Container $container)
    {
        parent::setDependencies($container);

        $this->baseUrl = $container['base-url'];
    }

    /**
     * Retrieve display value.
     *
     * @return string
     */
    public function displayVal()
    {
        $val = parent::displayVal();

        if (!parse_url($val, PHP_URL_SCHEME)) {
            $uri = $this->baseUrl;

            return $uri->withPath($val);
        }

        return $val;
    }
}
