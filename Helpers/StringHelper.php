<?php

namespace Skvn\Base\Helpers;

class StringHelper
{
    protected static $snake = [];
    protected static $studly = [];


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

    public static function studly($value)
    {
        $key = $value;

        if (isset(static::$studly[$key])) {
            return static::$studly[$key];
        }

        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return static::$studly[$key] = str_replace(' ', '', $value);
    }


}