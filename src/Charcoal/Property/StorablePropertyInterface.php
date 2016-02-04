<?php

namespace Charcoal\Property;

/**
 *
 */
interface StorablePropertyInterface
{
    /**
     * @return array
     */
    public function fields();

    /**
     * @param string $fieldIdent The property field identifier.
     * @return mixed
     */
    public function fieldVal($fieldIdent);

    /**
     * @param mixed $val Optional. The value to convert to storage value.
     * @return mixed
     */
    public function storageVal($val = null);

    /**
     * @return string
     */
    public function sqlExtra();

    /**
     * @return string
     */
    public function sqlType();

    /**
     * @return integer
     */
    public function sqlPdoType();
}
