<?php

namespace NW;

use JsonException;
use NW\Response\Response;

abstract class Controller
{
    /** @var string Месторасположение дирректории с видами */
    private $dir = __DIR__ . '/../../views/';

    /** @var string Месторасположение дирректории, где хранится html-кеш */
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
    
    /**
     * Задает текущее время
     *
     * Controller constructor.
     */
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
     */
    public function render($view, $params = [], int $statusCode = null): Response
    {
        extract($params, EXTR_OVERWRITE);

        // TODO Добавить проверку на наличие вида
        ob_start();

        require $this->dir . $this->templates . $view . '.php';

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
     * Вовзаращет ответ в виде json, никакие виды и шаблоны в этом случае не нужны
     *
     * @param array $json
     * @return Response
     * @throws JsonException
     */
    public function json(array $json): Response
    {
        $response = new Response(json_encode($json, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
        $response->withHeader('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Делает редирект на указанный URI
     *
     * @param string $uri
     */
    public function redirect(string $uri): void
    {
        header('Location: ' . HOST . $uri);
    }

    /**
     * Возвращает страницу 404
     *
     * @param string $error
     * @return Response
     */
    public function pageNotFound(string $error = ''): Response
    {
        // На всякий случай переключаем шаблон на базовый (т.к. 404 ошибка может кидаться и с других шаблонов)
        $this->layout_url = 'layout/main.php';

        return $this->render('errors/404', ['error' => $error], 404);
    }
    
    /**
     * Проверяет наличие кэша по его имени (и id если есть)
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

        // TODO Потестировать работу кэша. Вроде работает, но у меня какие-то сомнения

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
     */
    protected function createCache($name, $content, $id = null): void
    {
        if ($id) {
            $name .= '_' . $id;
        }

        $file = fopen($this->cache . $name, 'wb');
        fwrite($file, $content . '=кэш=');
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
