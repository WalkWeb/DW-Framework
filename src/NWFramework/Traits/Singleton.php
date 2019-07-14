<?php

namespace NW\Traits;

trait Singleton
{
    private static $instance;

    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    private function __clone() {}
}
