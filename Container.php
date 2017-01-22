<?php

namespace Skvn\Base;

class Container implements \ArrayAccess
{
    use Traits\ArrayAccessImpl;

    protected $instances;

    protected static $instance = null;


    function __construct()
    {
        if (!is_null(static :: $instance)) {
            throw new Exceptions\Exception('Only one container instance can be created. User getInstance instead of direct creation.');
        }
    }

    static function getInstance()
    {
        if (is_null(static :: $instance)) {
            static :: $instance = new static();
        }
        return static :: $instance;
    }

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