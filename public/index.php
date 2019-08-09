<?php

// Подключаем автозагрузку
require_once __DIR__ . '/../vendor/autoload.php';

// Подключаем настройки: если есть config.local.php - подключаем их, иначе - config.php
if (file_exists(__DIR__ . '/../config.local.php')) {
    require_once __DIR__ . '/../config.local.php';
} else {
    require_once __DIR__ . '/../config.php';
}

use NW\Runtime;
use NW\Request\ServerRequestFactory;
use NW\App\App;
use NW\Response\Emitter;

if (DEV) {
    Runtime::start();
}

// Создаем объект request на основе глобальных параметров
$request = ServerRequestFactory::fromGlobals();

// Создаем объект приложения
$app = new App();

// Получаем объект response на основе запроса
$response = $app->handle($request);

// Распечатываем response
Emitter::emit($response);
