<?php

declare(strict_types=1);

namespace Tests\utils;

use Exception;
use NW\AbstractModel;
use NW\Validator;

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
    /**
     * UUID
     *
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array[]
     */
    private static $rules = [
        'id' => [
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
     * @throws Exception
     */
    public function __construct(string $id)
    {
        parent::__construct();
        $this->create($id);
    }

    // TODO createFromData

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
        $this->db->query(
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
        $this->db->query(
            "DELETE FROM `books` WHERE `id` = ?",
            [['type' => 's', 'value' => $this->id]]
        );
    }

    /**
     * Заполняет модель данными на основе данных из базы
     *
     * @param string $id
     * @throws Exception
     */
    private function create(string $id): void
    {
        $query = $this->db->query(
            "SELECT `id`, `name` FROM `books` WHERE id = ?",
            [['type' => 's', 'value' => $id]],
            true
        );

        foreach (self::$rules as $property => $rule) {

            if (!array_key_exists($property, $query)) {
                throw new Exception("$property not found in data");
            }

            if (!Validator::check($property, $query[$property], $rule)) {
                throw new Exception(Validator::getError());
            }

            $this->$property = $query[$property];
        }
    }
}
