<?php

namespace Charcoal\Cms;

/**
 * Defines a renderable object associated to a template.
 */
interface TemplateableInterface
{
    /**
     * Set the renderable object's template identifier.
     *
     * @param mixed $template The template ID.
     * @return TemplateableInterface Chainable
     */
    public function setTemplateIdent($template);

    /**
     * Retrieve the renderable object's template identifier.
     *
     * @return mixed
     */
    public function templateIdent();

    /**
     * Customize the template's options.
     *
     * @param array|string $options Template options.
     * @return TemplateableInterface Chainable
     */
    public function setTemplateOptions($options);

    /**
     * Retrieve the template's customized options.
     *
     * @return array
     */
    public function templateOptions();
}
