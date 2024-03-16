<?php

// Подключаем автозагрузку
require_once __DIR__ . '/../vendor/autoload.php';

// Подключаем настройки: если есть config.local.php - подключаем их, иначе - config.php
if (file_exists(__DIR__ . '/../config.local.php')) {
    require_once __DIR__ . '/../config.local.php';
} else {
    require_once __DIR__ . '/../config.php';
}

use NW\Request;
use NW\Runtime;
use NW\App;

// Создаем контейнер
$container = App::createDefaultContainer();
$container->set(Runtime::class, new Runtime());

// Создаем объект request на основе глобальных параметров
$request = Request::fromGlobals();

// Подгружаем роуты
$router = require __DIR__ . '/../routes/web.php';

// Создаем объект приложения
$app = new App($router, $container);

// Получаем объект response на основе запроса
try {
    $response = $app->handle($request);

    // Распечатываем response
    App::emit($response);

} catch (Exception $e) {
    // TODO В обычном режиме возвращается заглушка 500/401/404 ошибки, а в DEV-режиме возвращаем детализацию ошибки
    echo $e->getMessage();
}
