<?php

/** Включение/выключение сайта */
define('APPLICATION_OFFLINE', false);

/** Указываем дирректорию приложения, т.к. она может меняться из-за контекста вызова скрипта */
define('DIR', __DIR__);

/** Сохранять ли логи */
define('LOGS', true);

/** Сохранять ли логи в файл */
define('LOGS_FILE', false);

/** Полный URL сайта */
define('HOST', 'http://nightworld.loc/');

/** Параметры подключения к БД */
define('DB_HOST', 'localhost');
define('DB_NAME', 'YOUR_DB_NAME');
define('DB_USER', 'YOUR_USER_NAME');
define('DB_PASSWORD', 'YOUR_PASSWORD');

/** Режим разработчика - true / продакшена - false */
define('DEV', true);

/** Базовый шаблон дизайна сайта */
define('TEMPLATES_DEFAULT', 'default');
