<?php

namespace Skvn\Base\Helpers;

use Skvn\Base\Exceptions\ConsoleException;

class Console
{
    protected static $formats = [
        'bold' => "\033[1m",
        'green' => "\033[32m",
        'red' => "\033[31m",
        'blue' => "\033[34m"
    ];

    static $endFormat = "\033[0m";


    static function parseInput($map = [])
    {
        $result = ['arguments' => [], 'options' => []];
        $args = $_SERVER['argv'] ?? [];
        if (count($args)) {
            array_shift($args);
        }
        while (count($args) > 0) {
            $arg = array_shift($args);
            if (Str :: pos('--', $arg) === 0) {
                $arg = substr($arg, 2);
                $scope = 'options';
            } elseif (Str :: pos('-', $arg) === 0) {
                $arg = substr($arg, 1);
                $scope = 'options';
            } else {
                $scope = 'arguments';
            }
            if (Str :: pos('=', $arg) !== false) {
                list($k, $v) = explode('=', $arg);
            } else {
                list($k, $v) = [$arg, true];
            }
            $result[$scope] = static :: addValue($result[$scope], $k, $v);
            if (!empty($map[$k])) {
                $result[$scope] = static :: addValue($result[$scope], $map[$k], $v);
            }
        }
        return $result;
    }

    protected static function addValue($arr, $key, $value)
    {
        if (array_key_exists($key, $arr)) {
            if (is_array($arr[$key])) {
                array_push($arr[$key], $value);
            } else {
                $arr[$key] = [$arr[$key], $value];
            }
        } else {
            $arr[$key] = $value;
        }
        return $arr;
    }

    static function hasColors()
    {
        if (DIRECTORY_SEPARATOR == '\\')
        {
            return false !== getenv('ANSICON') || 'ON' === getenv('ConEmuANSI');
        }

        return function_exists('posix_isatty') && @posix_isatty(STDOUT);
    }

    static function format($text, $format)
    {
        if (!isset(static :: $formats[$format])) {
            throw new ConsoleException('Unknown format: ' . $format);
        }
        return static :: $formats[$format] . $text . static :: $endFormat;
    }

    static function bold($text)
    {
        return static :: format('bold');
    }

    static function green($text)
    {
        return static :: format('green');
    }

    static function red($text)
    {
        return static :: format('red');
    }

    static function blue($text)
    {
        return static :: format('blue');
    }

    static function stdout($text)
    {
        if (self :: hasColors()) {
            if (Str :: contains('<', $text)) {
                $open = array_map(function($item){return '<' . $item . '>';}, array_keys(static :: $formats));
                $replace = array_values(static :: $formats);
                $close = array_map(function($item){return '</' . $item . '>';}, array_keys(static :: $formats));
                $text = str_replace($open, $replace, $text);
                $text = str_replace($close, static :: $endFormat, $text);
            }
        }
        echo $text . PHP_EOL;
    }




}