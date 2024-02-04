<?php

declare(strict_types=1);

namespace Tests\utils;

use Exception;
use NW\AbstractModel;
use NW\AppException;
use NW\Container;

/**
 * Данный класс предназначен для тестирования абстрактного NW\AbstractModel
 *
 * ВАЖНО: ORM в проекте не используется. По этому весь функционал создания/обновления/удаления модели нужно писать
 * вручную
 *
 * @package Tests\utils
 */
class ExampleModel extends AbstractModel
{
    private string $id;

    private string $name;

    private array $rules = [
        'id'   => [
            'required',
            'string',
            'min' => 36,
            'max' => 36,
        ],
        'name' => [
            'required',
            'string',
            'min' => 2,
            'max' => 15,
        ],
    ];

    /**
     * По умолчанию модель создается на основе данных из базы
     *
     * @param string $id
     * @param Container $container
     * @throws AppException
     */
    public function __construct(string $id, Container $container)
    {
        parent::__construct($container);
        $this->create($id);
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Сохраняет текущие параметры модели в базе. Подразумевается, что может изменяться только name
     *
     * @throws Exception
     */
    public function save(): void
    {
        $this->connection->query(
            "UPDATE `books` SET `name` = ? WHERE `id` = ?",
            [
                ['type' => 's', 'value' => $this->name],
                ['type' => 's', 'value' => $this->id],
            ]
        );
    }

    /**
     * Удаляет данные модели из базы (unset самой модели нужно делать отдельно)
     *
     * @throws Exception
     */
    public function remove(): void
    {
        $this->connection->query(
            "DELETE FROM `books` WHERE `id` = ?",
            [['type' => 's', 'value' => $this->id]]
        );
    }

    public function checkCache(string $name, int $time): bool
    {
        return parent::checkCache($name, $time);
    }

    public function createCache($name, $content): void
    {
        parent::createCache($name, $content);
    }

    public function deleteCache(string $name): void
    {
        parent::deleteCache($name);
    }

    /**
     * Заполняет модель данными на основе данных из базы
     *
     * @param string $id
     * @throws AppException
     */
    private function create(string $id): void
    {
        $validator = $this->container->getValidator();

        $query = $this->connection->query(
            "SELECT `id`, `name` FROM `books` WHERE id = ?",
            [['type' => 's', 'value' => $id]],
            true
        );

        foreach ($this->rules as $property => $rule) {

            if (!array_key_exists($property, $query)) {
                throw new AppException("$property not found in data");
            }

            if (!$validator->check($property, $query[$property], $rule)) {
                throw new AppException($validator->getError());
            }

            $this->$property = $query[$property];
        }
    }
}
