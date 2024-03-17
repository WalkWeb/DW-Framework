<?php

declare(strict_types=1);

namespace Migrations;

use NW\AppException;
use NW\Connection;

class Version_2024_03_17_11_05_34_26
{
    /**
     * Добавляется таблица posts
     *
     * TODO Добавить постам уникальность
     * TODO Добавить title_translate
     *
     * @param Connection $connection
     * @throws AppException
     */
    public function run(Connection $connection): void
    {
        $connection->query("
            CREATE TABLE IF NOT EXISTS `posts` (
                `id`         VARCHAR(36) NOT NULL,
                `title`      VARCHAR(255) NOT NULL,
                `text`       TEXT NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,   
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        echo "run Version_2024_03_17_11_05_34_26\n";
    }
}
