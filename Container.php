<?php

namespace Skvn\Base;

class Container
{
    protected $instances;

    function make($class)
    {
        if (!isset($this->instances[$class])) {
            $this->instances[$class] = $this->create($class);
        }
        return $this->instances[$class];
    }

    function create($class)
    {
        return new $class;
    }

}