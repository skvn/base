<?php

namespace Skvn\Base;

class ErrorHandler
{
    protected $scb_registered = false;

    function registerExceptionHandler($handler = null)
    {
        if (!is_callable($handler))
        {
            $handler = array($this, "handleDefault");
        }
        set_exception_handler($handler);
    }

    function registerFatalErrorHandler($handler = null)
    {
        if (!is_callable($handler))
        {
            $handler = array($this, "handleDefault");
        }
        if (!$this->scb_registered)
        {
            register_shutdown_function($handler);
            $this->scb_registered = true;
        }
    }

    function registerErrorHandler($handler = null)
    {
        if (!is_callable($handler))
        {
            $handler = array($this, "handleDefault");
        }
        set_error_handler($handler);
    }

    function unregisterErrorHandler()
    {
        restore_error_handler();
    }

    function handleDefault($e)
    {
        die("Йа померло");
    }

    static function convertErrorsToExceptions($errno, $errstr, $errfile, $errline)
    {
        if (!($errno & error_reporting()))
        {
            return false;
        }

        $params = array(
            "file" => $errfile,
            "line" => $errline,
        );
        throw new Exceptions\Exception($errstr, $params, $errno, 3);
    }

}