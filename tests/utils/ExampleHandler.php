<?php

declare(strict_types=1);

namespace Tests\utils;

use NW\AbstractHandler;
use NW\Container;
use NW\Request;
use NW\Response;

/**
 * Так как NW\Handler является абстрактным классом, мы не можем протестировать его напрямую. Для этого создан
 * ExampleHandler, который просто наследуется от NW\Handler
 *
 * @package Tests\utils
 */
class ExampleHandler extends AbstractHandler
{
    public function __invoke(Request $request): Response
    {
        return new Response('example html content');
    }

    public function getCache($name, $time, string $id = ''): string
    {
        return parent::getCache($name, $time, $id);
    }

    public function createCache(string $name, string $content, $id = null, string $prefix = ''): void
    {
        parent::createCache($name, $content, $id, $prefix);
    }

    public function redirect(string $url, string $body = '', int $code = Response::MOVED_PERMANENTLY): Response
    {
        return parent::redirect($url, $body, $code);
    }

    public function deleteCache($name = null): void
    {
        parent::deleteCache($name);
    }

    public function cacheWrapper($name, $id = null, $time = 0, string $prefix = ''): string
    {
        return parent::cacheWrapper($name, $id, $time, $prefix);
    }

    public function exampleAction(): Response
    {
        return new Response('example html content');
    }

    public function getContainer(): Container
    {
        return parent::getContainer();
    }

    public function setLayoutUrl(string $layoutUrl): void
    {
        $this->layoutUrl = $layoutUrl;
    }
}
