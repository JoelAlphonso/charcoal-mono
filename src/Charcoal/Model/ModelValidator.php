<?php

namespace Charcoal\Model;

use Charcoal\Validator\AbstractValidator as AbstractValidator;

/**
*
*/
class ModelValidator extends AbstractValidator
{
    /**
    * @return boolean
    */
    public function validate()
    {
        $model = $this->model;

        $props = $model->properties();

        $ret = true;
        foreach ($props as $ident => $p) {
            if (!$p ||  !$p->active()) {
                continue;
            }
            $valid = $p->validate();
            if ($valid === false) {
                $validator = $p->validator();
                $this->merge($validator, $ident);
                $ret = false;
            }
        }

        return $ret;
    }
}
