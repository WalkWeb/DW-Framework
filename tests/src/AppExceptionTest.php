<?php

declare(strict_types=1);

namespace Tests\src;

use WalkWeb\NW\AppException;
use WalkWeb\NW\Container;
use WalkWeb\NW\Response;
use WalkWeb\NW\Route\RouteCollection;
use WalkWeb\NW\Route\Router;
use Tests\AbstractTest;

class AppExceptionTest extends AbstractTest
{
    public function testAppExceptionCreate(): void
    {
        // default
        $default = new AppException($defaultMessage = 'default message');

        self::assertEquals($defaultMessage, $default->getMessage());
        self::assertEquals(Response::INTERNAL_SERVER_ERROR, $default->getCode());
        self::assertNull($default->getPrevious());

        // custom
        $custom = new AppException($customMessage = 'custom message', Response::BAD_GATEWAY, $default);

        self::assertEquals($customMessage, $custom->getMessage());
        self::assertEquals(Response::BAD_GATEWAY, $custom->getCode());
        self::assertEquals($default, $custom->getPrevious());
    }

    /**
     * @throws AppException
     */
    public function testAppExceptionPrintDefault(): void
    {
        $this->getApp(new Router(new RouteCollection()));
        $e = new AppException('message');

        $expectedContent = <<<EOT
<!DOCTYPE html>
<html lang="ru">
<head>
    <title>500: Internal Server Error</title>
    <meta name="Description" content="">
    <meta name="Keywords" content="">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <link rel="stylesheet" type="text/css" href="/styles/main.css">
</head>
<body>
<div class="content">
    <h1 class="center">500: Internal Server Error</h1>
    <p class="center">Кажется что-то сломалось. Попробуйте обратиться позже.</p>
</body>
</html>
EOT;

        ob_start();
        $e->printException($e);
        $content = ob_get_clean();

        self::assertEquals($expectedContent, $content);
    }

    /**
     * @throws AppException
     */
    public function testAppExceptionPrintDetails(): void
    {
        $this->getApp(new Router(new RouteCollection()));
        $e = new AppException('message', Response::UNAUTHORIZED);

        ob_start();
        $e->printException($e, Container::APP_DEV);
        $content = ob_get_clean();

        self::assertRegExp('/Ошибка/', $content);
    }
}
