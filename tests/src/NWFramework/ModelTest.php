<?php

declare(strict_types=1);

namespace Tests\src\NWFramework;

use Exception;
use NW\Connection;
use Tests\AbstractTestCase;
use Tests\utils\ExampleModel;

class ModelTest extends AbstractTestCase
{
    /**
     * Тест на создание модели на основе данных из базы
     *
     * @throws Exception
     */
    public function testModelCreateFromDBSuccess(): void
    {
        $id = '6e9043d1-18fb-44ea-be60-c356048f63a2';
        $name = 'Example#1';

        $db = new Connection();
        $db->autocommit(false);

        // Create table
        $this->createTable($db);

        // Insert data
        $this->insert($db, $id, $name);

        // Create model from db data
        $model = new ExampleModel($id, $db);

        self::assertEquals($id, $model->getId());
        self::assertEquals($name, $model->getName());

        $db->rollback();
    }

    /**
     * Тест на обновление модели
     *
     * @throws Exception
     */
    public function testModelUpdateSuccess(): void
    {
        // Для создания модели тест повторяет testModelCreateFromDBSuccess()
        $id = 'a5509cb2-d50a-46e0-95fd-f185726441cf';
        $name = 'BookName';

        $db = new Connection();
        $db->autocommit(false);

        $this->createTable($db);
        $this->insert($db, $id, $name);
        $model = new ExampleModel($id, $db);

        self::assertEquals($id, $model->getId());
        self::assertEquals($name, $model->getName());

        // Обновляем имя
        $newName = 'NewBookName';
        $model->setName($newName);
        $model->save();

        // Эта строчка не обязательна, но специально обозначаем, что старая модель удалена
        unset($model);

        // Создаем модель вновь и проверяем, что получаем новое имя
        $model = new ExampleModel($id, $db);
        self::assertEquals($newName, $model->getName());

        $db->rollback();
    }

    /**
     * @throws Exception
     */
    public function testModelUpdateRemove(): void
    {
        // Для создания модели тест повторяет testModelCreateFromDBSuccess()
        $id = '018f6cf1-6ace-4aa7-8234-acfae931276d';
        $name = 'RemoveBook';

        $db = new Connection();
        $db->autocommit(false);

        $this->createTable($db);
        $this->insert($db, $id, $name);
        $model = new ExampleModel($id, $db);

        self::assertEquals($id, $model->getId());
        self::assertEquals($name, $model->getName());

        // Удаляем модель
        $model->remove();
        unset($model);

        // При попытке создать модель - получаем ошибку
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('id not found in data');
        new ExampleModel($id, $db);

        $db->rollback();
    }

    /**
     * @param Connection $db
     * @param string $id
     * @param string $name
     * @throws Exception
     */
    private function insert(Connection $db, string $id, string $name): void
    {
        $db->query(
            'INSERT INTO `books` (`id`, `name`) VALUES (?, ?);',
            [
                ['type' => 's', 'value' => $id],
                ['type' => 's', 'value' => $name],
            ]
        );
    }

    /**
     * @param Connection $db
     * @throws Exception
     */
    private function createTable(Connection $db): void
    {
        $db->query('CREATE TABLE IF NOT EXISTS `books` (
            `id` VARCHAR(36) NOT NULL,
            `name` VARCHAR(255) NOT NULL 
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
    }
}
