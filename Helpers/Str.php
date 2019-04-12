<?php

namespace Skvn\Base\Helpers;

class Str
{
    protected static $snake = [];
    protected static $studly = [];
    protected static $camel = [];
    protected static $underscore = [];


    public static function pos($what, $where)
    {
        return strpos($where, $what);
    }

    public static function contains($what, $where)
    {
        foreach ((array) $what as $pattern) {
            if ($pattern != '' && mb_strpos($where, $pattern) !== false) {
                return true;
            }
        }
        return false;
    }

    public static function startsWith($what, $where)
    {
        foreach ((array) $what as $pattern) {
            if ($pattern != '' && strpos($where, $pattern) === 0 ) {
                return true;
            }
        }

        return false;
    }

    public static function snake($value)
    {
        $key = $value;

        if (isset(static::$snake[$key])) {
            return static::$snake[$key];
        }

        if (! ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', $value);

            $value = strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1-', $value));
        }

        return static::$snake[$key] = $value;
    }

    public static function underscore($value)
    {
        $key = $value;

        if (isset(static::$underscore[$key])) {
            return static::$underscore[$key];
        }

        if (! ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', $value);

            $value = strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1_', $value));
        }

        return static::$underscore[$key] = $value;
    }

    public static function camel($value)
    {
        if (isset(static::$camel[$value])) {
            return static::$camel[$value];
        }

        return static::$camel[$value] = lcfirst(static::studly($value));
    }


    public static function studly($value)
    {
        $key = $value;

        if (isset(static::$studly[$key])) {
            return static::$studly[$key];
        }

        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return static::$studly[$key] = str_replace(' ', '', $value);
    }

    public static function classBasename($class)
    {
        $class = is_object($class) ? get_class($class) : $class;

        return basename(str_replace('\\', '/', $class));
    }

    public static function xml2array($xml, $rootNode = null)
    {
        $arr = json_decode(json_encode(simplexml_load_string($xml)), true);
        return !empty($rootNode) ? ($arr[$rootNode] ?? []) : $arr;
    }

    public static function random($length)
    {
        $string = '';
        while (($len = strlen($string)) < $length) {
            $size = $length - $len;
            $bytes = random_bytes($size);
            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }
        return $string;
    }




}