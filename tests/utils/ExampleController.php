<?php

declare(strict_types=1);

namespace Tests\utils;

use NW\AbstractController;
use NW\Response\Response;
use NW\Utils\HttpCode;

/**
 * Так как NW\Controller является абстрактным классом, мы не можем протестировать его напрямую. Для этого создан
 * TestController, который просто наследуется от NW\Controller
 *
 * @package Tests\utils
 */
class ExampleController extends AbstractController
{
    public function getCache($name, $time, string $id = ''): string
    {
        return parent::getCache($name, $time, $id);
    }

    public function createCache(string $name, string $content, $id = null, string $prefix = ''): void
    {
        parent::createCache($name, $content, $id, $prefix);
    }

    public function redirect(string $url, string $body = '', int $code = HttpCode::FOUND): Response
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

    public function setLayoutUrl(string $layoutUrl): void
    {
        $this->layoutUrl = $layoutUrl;
    }
}
