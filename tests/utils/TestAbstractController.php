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
class TestAbstractController extends AbstractController
{
    public function checkCache($name, $time, $id = null)
    {
        return parent::checkCache($name, $time, $id);
    }

    public function createCache($name, $content, $id = null, string $prefix = ''): void
    {
        parent::createCache($name, $content, $id, $prefix);
    }

    public function redirect(string $url, string $body = '', int $code = HttpCode::FOUND): Response
    {
        return parent::redirect($url, $body, $code);
    }
}
