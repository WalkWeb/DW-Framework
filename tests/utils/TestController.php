<?php

declare(strict_types=1);

namespace Tests\utils;

use NW\Controller;

/**
 * Так как NW\Controller является абстрактным классом, мы не можем протестировать его напрямую. Для этого создан
 * TestController, который просто наследуется от NW\Controller
 *
 * @package Tests\utils
 */
class TestController extends Controller
{
    public function checkCache($name, $time, $id = null)
    {
        return parent::checkCache($name, $time, $id);
    }

    public function createCache($name, $content, $id = null, string $prefix = ''): void
    {
        parent::createCache($name, $content, $id, $prefix);
    }
}
