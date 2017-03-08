<?php

namespace Skvn\Base;

use Skvn\Base\Helpers\Str;

class Config implements \ArrayAccess
{
    use Traits\ArrayOrObjectAccessImpl;
    use Traits\AppHolder;

    protected $config = ['__files' => [], '__env' => [], '__instance' => [], '__flat' => []];

    public function set($key, $value = null)
    {
        $target = &$this->config;
        if (!empty($key) && !is_array($key) && !is_array($value)) {
            $split = explode('.', $key);
            $value = [array_pop($split) => $value];
            $key = implode('.', $split);
        }
        if (is_array($key) && is_null($value)) {
            $value = $key;
            $key = null;
        }
        if (!empty($key)) {
            $keys = explode('.', $key);
            while (count($keys)) {
                $k = array_shift($keys);
                if (!isset($target[$k])) {
                    $target[$k] = [];
                }
                $target = &$target[$k];
            }
        }
        $this->config['__flat'] = array_replace($this->config['__flat'], $this->flatten($value, !empty($key) ? $key . '.' : ''));
        $target = array_replace_recursive($target, $value);
    }



    protected function flatten(&$array, $prepend = '')
    {
        $results = [];
        if (!is_array($array)) {
            return [substr($prepend, 0, -1) => $array];
        }

        foreach ($array as $key => &$value) {
            if (Str :: pos('__', $key) === 0) {
                continue;
            }
            if (is_array($value) /*&& !empty($value)*/) {
                $results = array_merge($results, $this->flatten($value, $prepend.$key.'.'));
            } else {
                if (Str :: pos('#', $value) === 0) {
                    $instanceKey = substr($value, 1);
                    $default = null;
                    if (Str :: pos('|', $instanceKey) !== false) {
                        list($instanceKey, $default) = explode('|', $instanceKey, 2);
                    }
                    if (isset($this->config['__instance'][$instanceKey])) {
                        $value = $this->config['__instance'][$instanceKey];
                        $array[$key] = $this->config['__instance'][$instanceKey];
                    } else {
                        $value = $default;
                        $array[$key] = $default;
                    }
                }
                $results[$prepend.$key] = $value;
            }
        }

        return $results;
    }


    public function get($key, $default = null)
    {
        if (array_key_exists($key, $this->config['__flat'])) {
            return $this->config['__flat'][$key];
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

    function env($name = null, $value = null, $append = false)
    {
        if (is_null($name)) {
            return $this->config['__env'];
        }
        if (!is_null($value)) {
            if ($append) {
                if (!isset($this->config['__env'][$name])) {
                    $this->config['__env'][$name] = [];
                }
                if ($append === true) {
                    $this->config['__env'][$name][] = $value;
                } else {
                    $this->config['__env'][$name][$append] = $value;
                }
            } else {
                $this->config['__env'][$name] = $value;
            }
        }
        return $this->config['__env'][$name] ?? null;
    }


    function loadInstanceConfig($filename)
    {
        $this->config['__instance'] = parse_ini_file($filename);
    }

    public function load($filename, $namespace = null)
    {
        if (array_key_exists($filename, $this->config['__files'])) {
            return;
        }
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
        $this->config['__files'][$filename] = true;
        if (!$this->valid($config)) {
            throw new Exceptions\InvalidArgumentException('Configuration in ' . $filename . ' is missed or invalid');
        }

        return $this->set($namespace, $config);
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

    function export()
    {
        return $this->config;
    }

    function import($config)
    {
        $this->config = $config;
    }


}