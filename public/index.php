<?php

// Подключаем автозагрузку
require_once __DIR__ . '/../vendor/autoload.php';

// Подключаем настройки
require_once __DIR__ . '/../config.php';

use NW\Runtime;
use NW\Request\ServerRequestFactory;
use NW\App\App;
use NW\Response\Emitter;

if (!APPLICATION_OFFLINE && DEV) {
    Runtime::start();
}

if (!APPLICATION_OFFLINE) {

    // Создаем объект request на основе глобальных параметров
    $request = ServerRequestFactory::fromGlobals();

    // Создаем объект приложения
    $app = new App();

    // Получаем объект response на основе запроса
    $response = $app->handle($request);

    // Распечатываем response
    Emitter::emit($response);

} else {
    die('Сайт временно отключен');
}
