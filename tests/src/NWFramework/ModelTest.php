<?php

declare(strict_types=1);

namespace Tests\src\NWFramework;

use Exception;
use NW\AppException;
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
        $table = 'books';
        $container = $this->getContainer();

        $connection = $container->getConnection();
        $connection->autocommit(false);

        // Create table
        $this->createTable($connection);

        // Clear table
        $this->clearTable($connection, $table);

        // Insert data
        $this->insert($connection, $id, $name);

        // Create model from db data
        $model = new ExampleModel($id, $container);

        self::assertEquals($id, $model->getId());
        self::assertEquals($name, $model->getName());

        $connection->rollback();
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
        $container = $this->getContainer();

        $db = $container->getConnection();
        $db->autocommit(false);

        $this->createTable($db);
        $this->insert($db, $id, $name);
        $model = new ExampleModel($id, $container);

        self::assertEquals($id, $model->getId());
        self::assertEquals($name, $model->getName());

        // Обновляем имя
        $newName = 'NewBookName';
        $model->setName($newName);
        $model->save();

        // Эта строчка не обязательна, но специально обозначаем, что старая модель удалена
        unset($model);

        // Создаем модель вновь и проверяем, что получаем новое имя
        $model = new ExampleModel($id, $container);
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
        $container = $this->getContainer();

        $db = $container->getConnection();
        $db->autocommit(false);

        $this->createTable($db);
        $this->insert($db, $id, $name);
        $model = new ExampleModel($id, $container);

        self::assertEquals($id, $model->getId());
        self::assertEquals($name, $model->getName());

        // Удаляем модель
        $model->remove();
        unset($model);

        // При попытке создать модель - получаем ошибку
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('id not found in data');
        new ExampleModel($id, $container);

        $db->rollback();
    }

    /**
     * @throws AppException
     */
    public function testModelNoCache(): void
    {
        $id = '018f6cf1-6ace-4aa7-8234-acfae931276d';
        $name = 'RemoveBook';
        $container = $this->getContainer();
        $db = $container->getConnection();
        $this->createTable($db);
        $this->insert($db, $id, $name);

        $model = new ExampleModel($id, $container);

        self::assertFalse($model->checkCache('text', 123));
    }
}
