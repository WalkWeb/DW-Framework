<?php

// Подключаем автозагрузку
require_once __DIR__ . '/../vendor/autoload.php';

// Подключаем настройки: если есть config.local.php - подключаем их, иначе - config.php
if (file_exists(__DIR__ . '/../config.local.php')) {
    require_once __DIR__ . '/../config.local.php';
} else {
    require_once __DIR__ . '/../config.php';
}

use WalkWeb\NW\Container;
use WalkWeb\NW\Request;
use WalkWeb\NW\Runtime;
use WalkWeb\NW\App;

// exception не перехватываются - их корректная обработка для разных режимов (APP_ENV=prod/dev/test) происходит в AppException

// Создаем контейнер
$container = Container::create();
$container->set(Runtime::class, new Runtime());

// Создаем объект request на основе глобальных параметров
$request = Request::fromGlobals();

// Подгружаем роуты
$router = require __DIR__ . '/../routes/web.php';

// Создаем объект приложения
$app = new App($router, $container);

// Получаем объект ответа на основе запроса
$response = $app->handle($request);

// Распечатываем ответ
App::emit($response);
