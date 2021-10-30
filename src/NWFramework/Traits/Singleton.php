<?php

namespace NW\Traits;

// TODO На удаление?

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
