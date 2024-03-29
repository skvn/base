<?php

namespace Skvn\Base\Exceptions;

use Exception as PhpException;
use Skvn\Base\Backtrace;

class Exception extends PhpException
{
    protected $original_message;
    protected $params = [];
    protected $backtrace;

    function __construct($message, $details = [], $code = 0, $hide_calls_count = 0)
    {
        $this->original_message = $message;
        $this->params = $details;

        $this->backtrace = array_slice(debug_backtrace(), $hide_calls_count);

        foreach ($this->backtrace as $item) {
            if (isset($item['file']))
            {
                $this->file = $item['file'];
                $this->line = $item['line'];
                break;
            }
        }

        $message = $this->toNiceString();

        parent :: __construct($message, $code);
    }

    function getOriginalMessage()
    {
        return $this->original_message;
    }

    function getRealFile()
    {
        return $this->file;
    }

    function getRealLine()
    {
        return $this->line;
    }

    function getParams()
    {
        return $this->params;
    }

    function getParam($name)
    {
        return $this->params[$name] ?? null;
    }

    function getBacktrace() {
        return $this->backtrace;
    }

    function toNiceString($without_backtrace = false) {
        $message = $this->getOriginalMessage();
        if (!is_string($message)) {
            $message = json_encode($message);
        }
        $string = get_class($this) . ': ' . $message . PHP_EOL;
        if ($this->params) {
            $string .= 'Additional params: ' . PHP_EOL . Backtrace::var_export($this->params) . PHP_EOL;
        }
        if (!$without_backtrace) {
            $string .= 'Backtrace: ' . PHP_EOL . $this->getTraceAsString();
        }
        return $string;
    }

    function __toString()
    {
        return $this->toNiceString();
    }


}