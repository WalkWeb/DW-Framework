<?php

namespace NW;

use Exception;
use NW\Response\Response;
use NW\Response\ResponseException;
use NW\Utils\HttpCode;

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
    protected $layoutUrl = 'layout/main.php';

    /** @var string - Тип возвращаемых данных html /json */
    protected $dataType = 'html';

    public function __construct()
    {
        $this->time = microtime(true);
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

        if ($this->layout && !file_exists($this->dir . $this->templates . $this->layoutUrl)) {
            throw new \NW\Exception("Layout не найден: $viewPath");
        }

        ob_start();

        require $viewPath;

        // Помещаем страницу в общий макет сайта
        if ($this->layout) {

            $content = ob_get_clean();
            ob_start();

            require $this->dir . $this->templates . $this->layoutUrl;
        }

        $response = new Response(ob_get_clean());

        if ($statusCode !== null) {
            $response->setStatusCode($statusCode);
        }

        return $response;
    }

    /**
     * Возвращает ответ в виде json
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
        $this->layoutUrl = 'layout/main.php';

        return $this->render('errors/404', ['error' => $error], $code);
    }

    /**
     * Делает редирект на указанный URL
     *
     * @param string $url
     * @param string $body
     * @param int $code
     * @return Response
     * @throws ResponseException
     */
    protected function redirect(string $url, string $body = '', int $code = HttpCode::FOUND): Response
    {
        $response = new Response($body, $code);
        $response->withHeader('Location', $url);
        return $response;
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
     * @param string $name
     * @param string $content
     * @param null $id
     * @param string $prefix - Параметр для отладки и тестов, чтобы отличить контент который берется из кэша
     */
    protected function createCache(string $name, string $content, $id = null, string $prefix = ''): void
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
     * @param string $name
     * @throws \NW\Exception
     */
    protected function deleteCache(string $name): void
    {
        if (!file_exists($this->cache . $name)) {
            // TODO В будущем можно создать отдельный ControllerException
            throw new \NW\Exception('Указанного кэша не существует: ' . $this->cache . $name);
        }

        if ($name) {
            unlink($this->cache . $name);
        }
    }

    /**
     * Кэширующая обертка над методом (подразумевается, что метод возвращает html-контент для отображения) контроллера
     *
     * Проверяет наличие кеша и его актуальность - если есть - возвращает кэш, если нет - выполняет метод
     * создающий html-контент, создает кэш, возвращает контент.
     *
     * Задумка применения: к примеру, у нас есть страница поста с какими-то комментариями. Чтобы каждый раз не делать
     * запросы в базу - берем контент из кэша (делаем обращение через этот метод), а если пост изменился или добавился
     * комментарий - удаляем кэш. При следующем запросе к странице он создается вновь.
     *
     * @param string $controllerAction
     * @param null $id
     * @param int $time
     * @param string $prefix
     * @return string
     */
    protected function cacheWrapper(string $controllerAction, $id = null, $time = 0, string $prefix = ''): string
    {
        $content = $this->checkCache($controllerAction, $time, $id);

        if ($content) {
            return $content;
        }

        $funcName = ucfirst($controllerAction);

        /** @var Response $response */
        $response = $this->$funcName();

        $this->createCache($controllerAction, $response->getBody(), $id, $prefix);

        return $response->getBody();
    }
}
