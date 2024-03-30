<?php

declare(strict_types=1);

namespace Tests\handlers\User;

use NW\AppException;
use NW\Request;
use NW\Response;
use Tests\AbstractTestCase;

class UserRegistrationHandlerTest extends AbstractTestCase
{
    /**
     * Тест на отображение формы регистрации
     *
     * @throws AppException
     */
    public function testUserRegistrationHandlerCreateForm(): void
    {
        $request = new Request(['REQUEST_URI' => '/registration']);
        $response = $this->app->handle($request);

        $expectedBody = <<<EOT
<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Регистрация</title>
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
        <li><a href="/registration" title="">Регистрация</a></li>
    </ul>
</div>
<div class="content">
    <h1>Регистрация</h1>
<form method="POST" action="/registration">
    <label>Login:<br /><input name="login" autocomplete="off" value=""></label>
    <label>Email:<br /><input name="email" autocomplete="off" value=""></label>
    <label>Password:<br /><input name="password" autocomplete="off" value="" type="password"></label>

    <button>Зарегистрироваться</button>
</form>
    <hr color="#444">
</div>
</body>
</html>
EOT;

        self::assertEquals($expectedBody, $response->getBody());
        self::assertEquals(Response::OK, $response->getStatusCode());
    }
}
