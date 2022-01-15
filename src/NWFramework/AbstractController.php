<?php

namespace NW;

use Exception;
use NW\Response\Response;

abstract class AbstractController
{
    /** @var string Месторасположение директории с вьюхами */
    private $dir = __DIR__ . '/../../views/';

    /** @var string Месторасположение директории, где хранится html-кеш */
    private $cache = __DIR__ . '/../../cache/html/';

    /** @var string Шаблон (по умолчанию используется шаблон "old") */
    protected $templates = TEMPLATES_DEFAULT . '/';

    /** @var string Title */
    protected $title = '';

    /** @var string Description */
    protected $description = '';

    /** @var string Keywords */
    protected $keywords = '';

    /** @var mixed Текущее время (используется при работе с кэшем */
    protected $time;

    /** @var bool Данная настройка отвечает за то, рендерить ли шаблон в общем слое (true) или отдельно (false) */
    protected $layout = true;

    /** @var string Путь к шаблону */
    protected $layout_url = 'layout/main.php';

    /** @var string - Тип возвращаемых данных html /json */
    protected $dataType = 'html';

    /** @var array - middleware для контроллера */
    protected $middleware = [];

    public function __construct()
    {
        $this->time = microtime(true);
        $this->checkMiddleware();
    }

    /**
     * Объединяет шаблон страницы с данными и создает объект Response с содержимым страницы
     *
     * @param $view
     * @param array $params
     * @param int|null $statusCode
     * @return Response
     * @throws Exception
     */
    public function render($view, $params = [], int $statusCode = null): Response
    {
        extract($params, EXTR_OVERWRITE);

        $viewPath = $this->dir . $this->templates . $view . '.php';

        if (!file_exists($viewPath)) {
            throw new \NW\Exception("View не найден: $viewPath");
        }

        ob_start();

        require $viewPath;

        // Помещаем страницу в общий макет сайта
        if ($this->layout) {
            $content = ob_get_clean();
            ob_start();
            // TODO Добавить проверку на наличие слоя
            require $this->dir . $this->templates . $this->layout_url;
        }

        $response = new Response(ob_get_clean());

        if ($statusCode !== null) {
            $response->setStatusCode($statusCode);
        }

        return $response;
    }

    /**
     * Вовзаращет ответ в виде json
     *
     * @param array $json
     * @return Response
     * @throws Exception
     */
    public function json(array $json): Response
    {
        $response = new Response(json_encode($json, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
        $response->withHeader('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Возвращает страницу 404
     *
     * @param string $error
     * @param int $code
     * @return Response
     * @throws Exception
     */
    public function renderErrorPage(string $error = '', int $code = 404): Response
    {
        // На всякий случай переключаем шаблон на базовый (т.к. 404 ошибка может кидаться и с других шаблонов)
        $this->layout_url = 'layout/main.php';

        return $this->render('errors/404', ['error' => $error], $code);
    }

    /**
     * Делает редирект на указанный URI
     *
     * TODO Редирект рабочий, но очень хардкорный. Подумать над улучшением
     *
     * @param string $uri
     */
    public function redirect(string $uri): void
    {
        header('Location: ' . HOST . $uri);
    }

    /**
     * Проверяет наличие кэша по его имени (и id если есть)
     *
     * TODO Метод возвращает bool|string - уйти от такой неоднозначности. Пусть проверяет только наличие кэша, а
     * TODO получение кэша пусть делается отдельным методом
     *
     * @param $name
     * @param null $id
     * @param $time
     * @return bool|string
     */
    protected function checkCache($name, $time, $id = null)
    {
        if ($id) {
            $name .= '_' . $id;
        }

        // Проверяем, есть ли кэш
        if (file_exists($this->cache . $name)) {

            // Проверяем, не просрочен ли он
            if (!($time > 0) || (($this->time - $time) < filemtime($this->cache . $name))) {
                return file_get_contents($this->cache . $name);
            }
        }

        return false;
    }

    /**
     * Создает кэш
     *
     * @param $name
     * @param $content
     * @param null $id
     * @param string $prefix - Параметр для отладки и тестов, чтобы отличить контент который берется из кэша
     */
    protected function createCache($name, $content, $id = null, string $prefix = ''): void
    {
        if ($id) {
            $name .= '_' . $id;
        }

        $file = fopen($this->cache . $name, 'wb');
        fwrite($file, $content . $prefix);
        fclose($file);
    }

    /**
     * Удаляет кэш
     *
     * @param null $name
     */
    protected function deleteCache($name = null): void
    {
        if ($name) {
            unlink($this->cache . $name);
        }
    }

    /**
     * Проверяет наличие кеша и его актуальность - если есть - возвращает, если нет - выполняет метод
     * создающий html-контент, создает кэш, возвращает контент.
     *
     * @param $name
     * @param null $id
     * @param int $time
     * @return string
     */
    protected function cacheHTML($name, $id = null, $time = 0): string
    {
        $content = $this->checkCache($name, $time, $id);

        if ($content) {
            return $content;
        }

        $funcName = ucfirst($name);

        $content = $this->$funcName();

        $this->createCache($name, $content, $id);

        return $content;
    }

    /**
     * Проходит по Middleware, если они есть, и выполняет их проверки
     */
    private function checkMiddleware(): void
    {
        if (count($this->middleware) > 0) {
            foreach ($this->middleware as $middleware) {
                $middleware = new $middleware();
                $middleware();
            }
        }
    }
}
