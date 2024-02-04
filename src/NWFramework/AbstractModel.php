<?php

namespace NW;

abstract class AbstractModel
{
    /**
     * Место хранения кэша результатов запросов
     *
     * TODO Вынести директорию хранения кэша в контейнер
     *
     * @var string
     */
    private string $cache = __DIR__ . '/../cache/sql/';

    protected Container $container;

    protected Connection $connection;

    /**
     * Текущее время
     *
     * @var mixed
     */
    protected $time;

    /**
     * Создаем объект подключения к базе и обработки запросов
     *
     * @param Container $container
     * @throws AppException
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->connection = $container->getConnection();
        $this->time = microtime(true);
    }

    /**
     * Кэширующая обертка над методом (подразумевается, что метод возвращает результат sql-запроса) модели
     *
     * Принимает имя запроса и параметры. Проверяет, есть ли кэш с таким именем (и не просрочен ли он), если есть -
     * возвращает его. Если нет - выполняет запрос (метод с соответствующим именем должен быть создан отдельно) и
     * кэширует его.
     *
     * @param string $modelMethod
     * @param $param
     * @param int $time
     * @return bool|mixed
     */
    public function cacheWrapper(string $modelMethod, $param, $time = 60)
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
     * @param string $name
     * @param int $time
     * @return bool|mixed
     */
    protected function checkCache(string $name, int $time): bool
    {
        // Проверяем, есть ли кэш
        $filePath = $this->cache . $name;
        if (file_exists($filePath)) {

            // Проверяем, не протух ли кэш
            if (!($time > 0) || (($this->time - $time) < filemtime($this->cache . $name))) {
                return unserialize(file_get_contents($filePath));
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
    protected function createCache(string $name, string $content): void
    {
        $content = serialize($content);

        $file = fopen($this->cache . $name, 'wb');
        fwrite($file, $content);
        fclose($file);
    }

    /**
     * Удаляет кэш по его имени.
     *
     * @param string $name
     */
    protected function deleteCache(string $name): void
    {
        // TODO Проверка наличия файла

        if ($name) {
            unlink($this->cache . $name);
        }
    }
}
