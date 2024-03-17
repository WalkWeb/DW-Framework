<?php

declare(strict_types=1);

namespace Migrations;

use NW\AppException;
use NW\Connection;

class Version_2024_02_04_14_08_11_42
{
    /**
     * Добавляются таблицы users и books
     *
     * @param Connection $connection
     * @throws AppException
     */
    public function run(Connection $connection): void
    {
        $connection->query("
            CREATE TABLE IF NOT EXISTS `users` (
                `id`   VARCHAR(36) NOT NULL,
                `name` VARCHAR(255) NOT NULL 
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $connection->query("
            CREATE TABLE IF NOT EXISTS `books` (
                `id`   VARCHAR(36) NOT NULL,
                `name` VARCHAR(255) NOT NULL 
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        echo "run Version_2024_02_04_14_08_11_42\n";
    }
}
