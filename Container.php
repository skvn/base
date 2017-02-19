<?php

namespace Skvn\Base;

abstract class Container implements \ArrayAccess
{
    use Traits\ArrayOrObjectAccessImpl;

    protected $instances = [];
    protected $aliases = [];
    protected $tools = [];
    protected $toolSignatures = [];


    protected static $instance = null;


    function __construct(...$args)
    {
        if (!is_null(static :: $instance)) {
            throw new Exceptions\Exception('Only one container instance can be created. Use getInstance instead of direct creation.');
        }
        static :: $instance = $this;
        $this->init(...$args);
    }


    static function getInstance()
    {
        if (is_null(static :: $instance)) {
            throw new Exceptions\Exception('Container instance must be directly created once');
        }
        return static :: $instance;
    }

    static function instance()
    {
        return static :: getInstance();
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

    function alias($alias, $instance)
    {
        if (!is_object($instance)) {
            $instance = $this->create($instance);
        }
        $this->aliases[$alias] = $instance;
    }

    function get($alias)
    {
        if (!isset($this->aliases[$alias])) {
            throw new Exceptions\NotFoundException('Alias ' . $alias . ' not found');
        }
        return $this->aliases[$alias];
    }

    function registerTool($tool)
    {
        $class = get_class($tool);
        $this->tools[$class] = $tool;
        foreach (get_class_methods($tool) as $method) {
            $this->toolSignatures[$method] = $class;
        }
    }

    function __call($method, $args = array())
    {
        if (isset($this->toolSignatures[$method])) {
            return call_user_func_array([
                                            $this->tools[$this->toolSignatures[$method]],
                                            $method
                                         ], $args);
        }
        throw new Exceptions\NotFoundException("No such method '$method' exists in container");
    }


}