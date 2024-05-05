<?php

declare(strict_types=1);

namespace Migrations;

use WalkWeb\NW\AppException;
use WalkWeb\NW\MySQL\Connection;

class Version_2024_03_17_11_05_34_26
{
    /**
     * Добавляется таблица posts
     *
     * @param Connection $connection
     * @throws AppException
     */
    public function run(Connection $connection): void
    {
        $connection->query("
            CREATE TABLE IF NOT EXISTS `posts` (
                `id`         VARCHAR(36) NOT NULL PRIMARY KEY ,
                `title`      VARCHAR(255) NOT NULL, -- TODO Заменить до 50
                `slug`       VARCHAR(255) NOT NULL UNIQUE,
                `text`       TEXT NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,   
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        echo "run Version_2024_03_17_11_05_34_26\n";
    }
}
