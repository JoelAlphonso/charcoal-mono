<?php

namespace Charcoal\Admin\Property\Display;

use \Charcoal\Admin\Ui\ImageAttributesTrait;
use \Charcoal\Admin\Property\AbstractPropertyDisplay;

/**
 * Image Display Property
 */
class ImageDisplay extends AbstractPropertyDisplay
{
    use ImageAttributesTrait;

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
