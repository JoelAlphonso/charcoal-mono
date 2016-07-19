<?php

namespace Charcoal\Admin\Widget;

use \Charcoal\Admin\Widget\ObjectFormWidget;

/**
 * The quick form widget for editing objects on the go.
 */
class QuickFormWidget extends ObjectFormWidget
{
    /**
     * @param array|ArrayInterface $data The widget data.
     * @return ObjectForm Chainable
     */
    public function setData($data)
    {
        $data = array_merge($_GET, $data);
        $data = array_merge($_POST, $data);
        parent::setData($data);
        return $this;
    }
}
