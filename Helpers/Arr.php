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

    public function sortBy(array $array, $column, $desc = false, $options = SORT_REGULAR)
    {
        $results = [];
        foreach ($array as $key => $value) {
            $results[$key] = $value[$column];
        }
        $desc ? arsort($results, $options) : asort($results, $options);
        foreach (array_keys($results) as $key) {
            $results[$key] = $array[$key];
        }
        return $results;
    }


}