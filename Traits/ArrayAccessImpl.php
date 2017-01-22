<?php

namespace Skvn\Base\Traits;

trait ArrayAccessImpl
{

    function offsetExists($offset)
    {
        return !is_null($this->get($offset));
    }

    function offsetGet($offset)
    {
        return $this->get($offset);
    }

    function offsetSet($offset, $value)
    {
        return $this->set($offset, $value);
    }

    function offsetUnset($offset)
    {
        return false;
    }

    function get($param)
    {
        return null;
    }

    function set($param, $value)
    {
        return false;
    }

}