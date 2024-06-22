<?php

define('APP_ENV', 'prod');

/** Указываем дирректорию приложения, т.к. она может меняться из-за контекста вызова скрипта */
define('DIR', __DIR__);

/** Сохранять ли логи */
define('LOGS', false);

/** Сохранять ли логи в файл */
define('LOGS_FILE', false);

/** Полный URL сайта */
define('HOST', 'https://you-domain.com/');

/** Название проекта, используется, например, при отправке почты */
define('APP_NAME', 'You App Name');

/** Параметры подключения к БД, их может быть несколько */
define('DB_CONFIGS', [
    'default' => [
        'host'     => '127.0.0.1',
        'user'     => 'YOUR_DB_USER_NAME',
        'password' => 'YOUR_DB_PASSWORD',
        'database' => 'YOUR_DB_NAME',
    ],
]);

/** Базовый шаблон дизайна сайта */
define('TEMPLATE_DEFAULT', 'default');

/** Ключ для хэшей */
define('KEY', 'YOU_KEY');

/** Директория с кэшем */
define('CACHE_DIR', 'cache');

/** Директория с вьюхами */
define('VIEW_DIR', DIR . '/views/');

/** Директория с миграциями */
define('MIGRATION_DIR', DIR . '/migrations/');

/** Сохранять ли логи в файл */
define('SAVE_LOG', false);

/** Директория хранения файла */
define('LOG_DIR', __DIR__);

/** Имя файла для хранения логов */
define('LOG_FILE_NAME', 'logs');

/** Параметры для отправки почты */
define('MAIL_CONFIG', [
    'smtp_host'     => 'smtp_host',
    'smtp_port'     => 465,
    'smtp_auth'     => true,
    'smtp_user'     => 'smtp_user',
    'smtp_password' => 'smtp_password',
    'from'          => 'mail@mail.com',
]);
