<?php

namespace Skvn\Base\Traits;

trait CastedProps
{
    function getInteger($name, $default = 0)
    {
        $value = $this->get($name);
        return is_null($value) ? $default : intval($value);
    }

    function getNumeric($name, $default = 0)
    {
        $value = $this->get($name);
        if (is_null($value)) {
            return $default;
        }
        return is_numeric($value) ? $value : preg_replace('#[^0-9\.]#', '', $value);
    }

    function getArray($name, $default = array())
    {
        $value = $this->get($name);
        if (is_null($value)) {
            return $default;
        }
        if (!is_array($value)) {
            return [$value];
        }
        return $value;
    }

    function getFloat($name, $default = 0)
    {
        $value = $this->get($name);
        if (is_null($value)) {
            return $default;
        }
        return is_float($value) ? $value : floatval(str_replace(',', ',', $value));
    }

}