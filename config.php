<?php

define('APP_ENV', 'prod');

/** Указываем дирректорию приложения, т.к. она может меняться из-за контекста вызова скрипта */
define('DIR', __DIR__);

/** Сохранять ли логи */
define('LOGS', false);

/** Сохранять ли логи в файл */
define('LOGS_FILE', false);

/** Полный URL сайта */
define('HOST', 'https://dw-framework.ru/');

/** Параметры подключения к БД */
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'YOUR_DB_NAME');
define('DB_USER', 'YOUR_USER_NAME');
define('DB_PASSWORD', 'YOUR_PASSWORD');

/** Базовый шаблон дизайна сайта */
define('TEMPLATE_DEFAULT', 'default');

/** Ключ для хэшей */
define('KEY', 'YOU_KEY');

/** Директория с контролерами */
define('HANDLERS_DIR', 'Handlers');

/** Директория с middleware */
define('MIDDLEWARE_DIR', 'Middleware');

/** Директория с кэшем */
define('CACHE_DIR', 'cache');

/** Директория с кэшем */
define('VIEW_DIR', 'views');

/** Сохранять ли логи в файл */
define('SAVE_LOG', false);

/** Директория хранения файла */
define('LOG_DIR', __DIR__);

/** Директория хранения файла */
define('LOG_FILE_NAME', 'logs');
