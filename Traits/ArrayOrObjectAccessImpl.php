<?php

namespace Skvn\Base\Traits;

trait ArrayOrObjectAccessImpl
{
    #[\ReturnTypeWillChange]
    function offsetExists($offset)
    {
        return !is_null($this->get($offset));
    }

    #[\ReturnTypeWillChange]
    function offsetGet($offset)
    {
        return $this->get($offset);
    }

    #[\ReturnTypeWillChange]
    function offsetSet($offset, $value)
    {
        return $this->set($offset, $value);
    }

    #[\ReturnTypeWillChange]
    function offsetUnset($offset)
    {
        return false;
    }

    function __get($offset)
    {
        return $this->offsetGet($offset);
    }

    function __isset($offset)
    {
        return $this->offsetExists($offset);
    }

    function __set($offset, $value)
    {
        return $this->offsetSet($offset, $value);
    }

    function __unset($offset)
    {
        return $this->offsetUnset($offset);
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