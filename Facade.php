<?php

namespace Skvn\Base;

class Facade
{
    protected static $instances = [];

    protected static function getFacadeTarget()
    {
        throw new Exceptions\ImplementationExceptioin('Facade target not implemented for ' . static :: class);
    }

    protected static function getFacadeInstance()
    {
        $name = static :: getFacadeTarget();
        if (!isset(self :: $instances[$name])) {
            self :: $instances[$name] = Container :: getInstance()->get($name);
        }
        return self :: $instances[$name];
    }

    public static function __callStatic($method, $args)
    {
        $obj = static :: getFacadeInstance();
        return $obj->$method(...$args);
    }

}