<?php

namespace Skvn\Base\Traits;

use Skvn\Base\Exceptions\ConsoleException;
use Skvn\Base\Helpers\Str;

trait ConsoleOutput
{
    protected $consoleFormats = [
        'bold' => "\033[1m",
        'green' => "\033[32m",
        'red' => "\033[31m",
        'blue' => "\033[34m"
    ];

    protected $consoleEndFormat = "\033[0m";

    protected function consoleHasColors()
    {
        if (DIRECTORY_SEPARATOR == '\\')
        {
            return false !== getenv('ANSICON') || 'ON' === getenv('ConEmuANSI');
        }

        return function_exists('posix_isatty') && @posix_isatty(STDOUT);
    }

    protected function consoleFormat($text, $format)
    {
        if (!isset(static :: $consoleFormats[$format])) {
            throw new ConsoleException('Unknown format: ' . $format);
        }
        return static :: $consoleFormats[$format] . $text . static :: $colsoleEndFormat;
    }

    protected function consoleBold($text)
    {
        return $this->consoleFormat($text, 'bold');
    }

    protected function consoleGreen($text)
    {
        return $this->consoleFormat($text, 'green');
    }

    protected function consoleRed($text)
    {
        return $this->consoleFormat($text, 'red');
    }

    protected function consoleBlue($text)
    {
        return $this->consoleFormat($text, 'blue');
    }

    function stdout($text)
    {
        if ($this->consoleHasColors()) {
            if (Str :: contains('<', $text)) {
                $open = array_map(function($item){return '<' . $item . '>';}, array_keys($this->consoleFormats));
                $replace = array_values($this->consoleFormats);
                $close = array_map(function($item){return '</' . $item . '>';}, array_keys($this->consoleFormats));
                $text = str_replace($open, $replace, $text);
                $text = str_replace($close, $this->consoleEndFormat, $text);
            }
        }
        echo $text . PHP_EOL;
    }



}