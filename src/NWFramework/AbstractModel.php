<?php

namespace NW;

use mysqli;

abstract class AbstractModel
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
     * @throws AppException
     */
    public function __construct()
    {
        $this->db = Connection::getInstance();
        $this->time = microtime(true);
    }

    /**
     * Кэширующая обертка над методом (подразумевается, что метод возвращает результат sql-запроса) модели
     *
     * Принимает имя запроса и параметры. Проверяет, есть ли кэш с таким имененем (и не просрочен ли он), если есть -
     * возвращает его. Если нет - выполняет запрос (метод с соответствующим именем должен быть создан отдельно) и
     * кэширует его.
     *
     * @param $modelMethod
     * @param $param
     * @param int $time
     * @return bool|mixed
     */
    public function cacheWrapper($modelMethod, $param, $time = 60)
    {
        $content = $this->checkCache($modelMethod, $time);

        if ($content) {
            return $content;
        }

        $content = $this->$modelMethod($param);

        $this->createCache($modelMethod, $content);

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

            // Проверяем, не протух ли кэш
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
