<?php

namespace Skvn\Base\Helpers;

class Arr
{
    public static function keyBy($arr, $key)
    {
        $result = [];
        foreach ($arr as $v) {
            $result[$v[$key]] = $v;
        }
        return $result;
    }

    public static function isAssoc(array $array)
    {
        $keys = array_keys($array);
        return array_keys($keys) !== $keys;
    }

}