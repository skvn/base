<?php

namespace Skvn\Base\Traits;

trait ConstructorConfig
{

    protected $config;

    function __construct($config)
    {
        $this->config = $config;
        $this->init();
    }

    protected function init()
    {

    }

    function getConfig($param = null)
    {
        if (is_null($param)) {
            return $this->config;
        }
        return $this->config[$param] ?? null;
    }



}