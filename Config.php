<?php

namespace Skvn\Base;

class Config implements \ArrayAccess
{
    use Traits\ArrayAccessImpl;

    protected $config = [];
    protected $flatConfig = [];

    public function set($key, $value = null)
    {
        $data = is_array($key) ? $key : [$key => $value];
        foreach ($data as $k => $v) {
            $this->config[$k] = $v;
        }
        foreach ($this->flat($data) as $k => $v) {
            $this->flatConfig[$k] = $v;
        }
    }

    public function get($key, $default = null)
    {
        if (array_key_exists($key, $this->flatConfig)) {
            return $this->flatConfig[$key];
        }

        $array = $this->config;

        foreach (explode('.', $key) as $ns) {
            if (is_array($array) && array_key_exists($ns, $array)) {
                $array = $array[$ns];
            } else {
                return $default;
            }
        }

        return $array;
    }

    public function load($filename, $namespace = null)
    {
        $parts = explode('.', $filename);
        $ext = array_pop($parts);

        switch ($ext) {
            case 'php':
                $config = require($filename);
                break;
            case 'ini':
                $config = parse_ini_file($filename, true);
                break;
            default:
                throw new Exceptions\InvalidArgumentException('Unsupported configuration format in ' . $filename);
        }
        if (!$this->valid($config)) {
            throw new Exceptions\InvalidArgumentException('Configuration in ' . $filename . ' is missed or invalid');
        }


        if ($namespace) {
            $config = [$namespace => $config];
        }

        return $this->set($config);
    }

    protected function valid($value)
    {
        if (empty($value)) {
            return false;
        }
        if (is_array($value) || $value instanceof \ArrayAccess) {
            return true;
        }
        return false;
    }

    protected function flat($array, $prepend = '')
    {
        $results = [];

        foreach ($array as $key => $value) {
            if (is_array($value) && ! empty($value)) {
                $results = array_merge($results, $this->flat($value, $prepend.$key.'.'));
            } else {
                $results[$prepend.$key] = $value;
            }
        }

        return $results;
    }

}