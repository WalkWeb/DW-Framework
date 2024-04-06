<?php

declare(strict_types=1);

namespace Tests\handlers\User;

use Handlers\User\LoginPageHandler;
use Models\User\UserInterface;
use NW\AppException;
use NW\Request;
use NW\Response;
use Tests\AbstractTestCase;

class LoginPageHandlerTest extends AbstractTestCase
{
    /**
     * Тест на отображение формы авторизации
     *
     * @throws AppException
     */
    public function testLoginPageHandlerGetForm(): void
    {
        $request = new Request(['REQUEST_URI' => '/login']);
        $response = $this->app->handle($request);

        $expectedBody = <<<EOT
<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Вход</title>
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
    <h1>Вход</h1><form method="POST" action="/login">
            <label>Login:<br /><input name="login" autocomplete="off" value=""></label>
            <label>Password:<br /><input name="password" autocomplete="off" value="" type="password"></label>
        
            <button>Войти</button>
        </form>    <hr color="#444">
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
        self::assertEquals($expectedBody, $response->getBody());
    }

    /**
     * Тест на ситуацию, когда уже авторизованный пользователь открывает страницу авторизации
     *
     * @throws AppException
     */
    public function testLoginPageHandlerAlreadyAuth(): void
    {
        $token = 'VBajfT8P6PFtrkHhCqb7ZNwIFG45a5';
        $request = new Request(['REQUEST_URI' => '/login'], [], [UserInterface::AUTH_TOKEN => $token]);
        $response = $this->app->handle($request);

        self::assertEquals(Response::OK, $response->getStatusCode());
        self::assertRegExp('/' . LoginPageHandler::ALREADY_AUTH . '/', $response->getBody());
    }
}
