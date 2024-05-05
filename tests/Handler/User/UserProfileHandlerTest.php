<?php

declare(strict_types=1);

namespace Tests\Handler\User;

use Domain\User\UserInterface;
use WalkWeb\NW\AppException;
use WalkWeb\NW\Request;
use WalkWeb\NW\Response;
use Tests\AbstractTest;

class UserProfileHandlerTest extends AbstractTest
{
    /**
     * Тест на ситуацию, когда открывается страница профиля без авторизационного токена
     *
     * @throws AppException
     */
    public function testUserProfileHandlerNotAuth(): void
    {
        $request = new Request(['REQUEST_URI' => '/profile']);
        $response = $this->app->handle($request);

        self::assertEquals(Response::OK, $response->getStatusCode());
        self::assertRegExp('/Профиль/', $response->getBody());
        self::assertRegExp('/Вы не авторизованны/', $response->getBody());
    }

    /**
     * Тест на ситуацию, когда открывается страница профиля с неизвестным авторизационным токеном
     *
     * @throws AppException
     */
    public function testUserProfileHandlerAuthUnknownAuthToken(): void
    {
        $authToken = 'VBajfT8P6PFtrkHhCqb7ZNwIFG4xxx';
        $request = new Request(['REQUEST_URI' => '/profile'], [], [UserInterface::AUTH_TOKEN => $authToken]);
        $response = $this->app->handle($request);

        self::assertEquals(Response::OK, $response->getStatusCode());
        self::assertRegExp('/Профиль/', $response->getBody());
        self::assertRegExp('/Вы не авторизованны/', $response->getBody());
    }

    /**
     * Тест на ситуацию, когда открывается страница профиля с существующим авторизационным токеном
     *
     * @throws AppException
     */
    public function testUserProfileHandlerAuthAlready(): void
    {
        $authToken = 'VBajfT8P6PFtrkHhCqb7ZNwIFG45a5';
        $request = new Request(['REQUEST_URI' => '/profile'], [], [UserInterface::AUTH_TOKEN => $authToken]);
        $response = $this->app->handle($request);

        $expectedContent = <<<EOT
<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Профиль</title>
    <meta name="Description" content="">
    <meta name="Keywords" content="">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <link rel="stylesheet" type="text/css" href="/styles/main.css">
</head>
<body>
<div class="menu">
    <ul class="navigation">
        <li><a href="/" title="">Главная</a></li>
        <li><a href="/posts/1" title="">Посты</a></li>
        <li><a href="/post/create" title="">Создать пост</a></li>
        <li><a href="/cookies" title="">Cookies</a></li>
        <li><a href="/image" title="">Загрузка картинки</a></li>
        <li><a href="/login" title="">Вход</a></li>
        <li><a href="/registration" title="">Регистрация</a></li>
        <li><a href="/profile" title="">Профиль</a></li>
        <li><a href="/logout" title=""><img src="/images/logout.png" class="logout" alt="" /></a></li>
    </ul>
</div>
<div class="content">
    <h1>Профиль</h1><p><b>ID</b>: 23388e70-7171-4f14-bf13-39c1d77861bb<br />
    <b>Логин</b>: Login-1<br />
    <b>Email</b>: mail1@mail.com<br />
    <b>Email подтвержден?</b> нет<br />
    <b>Регистрация завершена?</b> нет<br />
    <b>Шаблон</b>: default<br />
    <b>Дата регистрации</b>: 2024-03-30 20:59:50</p>    <hr color="#444">
    <label>
        Дизайн:
        <select name="select" id="template">
            <option value="value2" selected>default</option>
            <option value="value3">light</option>
        </select>
    </label>
</div>
<script src="/js/main.js?v=1.00"></script>
</body>
</html>
EOT;

        self::assertEquals(Response::OK, $response->getStatusCode());
        self::assertEquals($expectedContent, $response->getBody());
    }
}
