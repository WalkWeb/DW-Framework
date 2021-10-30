<?php

namespace NW;

use mysqli;

abstract class Model
{
    /**
     * Место хранения кэша результатов запросов
     *
     * @var string
     */
    private $cache = __DIR__ . '/../cache/sql/';

    /** @var mysqli */
    public $db;

    /**
     * Текущее время
     *
     * @var mixed
     */
    private $time;

    /**
     * Создаем объект подключения к базе и обработки запросов
     *
     * TestQuery constructor.
     */
    public function __construct()
    {
        $this->db = Connection::getInstance();
        $this->time = microtime(true);
    }

    /**
     * Кешируемый запрос
     *
     * Принимает имя запроса и параметры. Проверяет, есть ли кэш с таким имененем (и не просрочен ли он), если есть -
     * возвращает его. Если нет - выполняет запрос (метод с соответствующим именем должен быть создан отдельно) и
     * кэширует его.
     *
     * @param $name
     * @param $param
     * @param int $time
     * @return bool|mixed
     */
    public function cacheQuery($name, $param, $time = 60)
    {
        $content = $this->checkCache($name, $time);

        if ($content) {
            return $content;
        }

        $content = $this->$name($param);

        $this->createCache($name, $content);

        return $content;
    }

    /**
     * Проверяет, существует ли кэш по имени запроса (если быть точнее - по имени метода, выполняющего SQL-запрос)
     *
     * @param $name
     * @param $time
     * @return bool|mixed
     */
    protected function checkCache($name, $time): bool
    {
        // TODO Потестировать работу кэша

        // Проверяем, есть ли кэш
        if (file_exists($this->cache . $name)) {

            // Проверяем, не просрочился ли кэш
            if (!($time > 0) || (($this->time - $time) < filemtime($this->cache . $name))) {
                return unserialize(file_get_contents($this->cache . $name));
            }
        }

        return false;
    }

    /**
     * Создает кэш с результатами SQL запроса
     *
     * @param $name
     * @param $content
     */
    protected function createCache($name, $content): void
    {
        $content = serialize($content);

        $file = fopen($this->cache . $name, 'wb');
        fwrite($file, $content);
        fclose($file);
    }

    /**
     * Удаляет кэш по его имени.
     *
     * @param null $name
     */
    protected function deleteCache($name): void
    {
        if ($name) {
            unlink($this->cache . $name);
        }
    }
}
