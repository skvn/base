<?php

namespace Skvn\Base\Traits;

use Skvn\Base\Container;

trait AppHolder
{
    /**
     * @var \Skvn\Base\Container application instance
     */
    protected $app;


    function getApp()
    {
        return $this->app;
    }

    function setApp(Container $app)
    {
        $this->app = $app;
    }
}