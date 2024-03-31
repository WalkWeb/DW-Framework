<?php

namespace NW;

use Exception;

abstract class AbstractHandler
{
    public const ERROR_MISS_VIEW   = 'View missing: %s';
    public const ERROR_MISS_LAYOUT = 'Layout missing: %s';
    public const ERROR_MISS_CACHE  = 'Cache missing: %s';

    // Месторасположение директории с вьюхами
    private const DIR = __DIR__ . '/../../views/';

    // Месторасположение директории, где хранится html-кеш
    private const CACHE_DIR = __DIR__ . '/../../cache/html/';

    /**
     * Шаблон (по умолчанию используется шаблон "old")
     *
     * Может подменяться через пользовательские настройки
     *
     * @var string
     */
    protected string $templates = TEMPLATE_DEFAULT . '/';

    /**
     * Title
     *
     * @var string
     */
    protected string $title = '';

    /**
     * Description
     *
     * @var string
     */
    protected string $description = '';

    /**
     * Keywords
     *
     * @var string
     */
    protected string $keywords = '';

    /**
     * Текущее время (используется при работе с кэшем
     *
     * @var float
     */
    protected float $time;

    /**
     * Данная настройка отвечает за то, рендерить ли шаблон в общем слое (true) или отдельно (false)
     *
     * @var bool
     */
    protected bool $layout = true;

    /**
     * Путь к шаблону
     *
     * @var string
     */
    protected string $layoutUrl = 'layout/main.php';

    /**
     * Тип возвращаемых данных html /json
     *
     * @var string
     */
    protected string $dataType = 'html';

    protected Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->time = microtime(true);
    }

    abstract public function __invoke(Request $request): Response;

    /**
     * Объединяет шаблон страницы с данными и создает объект Response с содержимым страницы
     *
     * @param $view
     * @param array $params
     * @param int|null $statusCode
     * @return Response
     * @throws AppException
     */
    public function render($view, $params = [], int $statusCode = null): Response
    {
        extract($params, EXTR_OVERWRITE);

        $viewPath = self::DIR . $this->templates . $view . '.php';

        if (!file_exists($viewPath)) {
            throw new AppException(sprintf(self::ERROR_MISS_VIEW, $viewPath));
        }

        if ($this->layout && !file_exists(self::DIR . $this->templates . $this->layoutUrl)) {
            throw new AppException(sprintf(self::ERROR_MISS_LAYOUT, $this->layoutUrl));
        }

        ob_start();

        require $viewPath;

        // Помещаем страницу в общий макет сайта
        if ($this->layout) {

            $content = ob_get_clean();
            ob_start();

            require self::DIR . $this->templates . $this->layoutUrl;
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
     * @throws AppException
     */
    public function renderErrorPage(string $error = '', int $code = Response::NOT_FOUND): Response
    {
        // На всякий случай переключаем шаблон на базовый (т.к. 404 ошибка может кидаться и с других шаблонов)
        $this->layoutUrl = 'layout/main.php';

        return $this->render('errors/404', ['error' => $error], $code);
    }

    /**
     * @return Container
     */
    protected function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * Делает редирект на указанный URL
     *
     * TODO Поменять местами body и code
     *
     * @param string $url
     * @param string $body
     * @param int $code
     * @return Response
     * @throws AppException
     */
    protected function redirect(string $url, string $body = '', int $code = Response::MOVED_PERMANENTLY): Response
    {
        $response = new Response($body, $code);
        $response->withHeader('Location', $url);
        return $response;
    }

    /**
     * Возвращает кэш если он есть. Если его нет - возвращает пустую строку
     *
     * @param $name
     * @param string $id
     * @param $time
     * @return string
     */
    protected function getCache($name, $time, string $id = ''): string
    {
        if ($id) {
            $name .= '_' . $id;
        }

        // Проверяем, есть ли кэш
        if (file_exists(self::CACHE_DIR . $name)) {

            // Проверяем, не просрочен ли он
            if (!($time > 0) || (($this->time - $time) < filemtime(self::CACHE_DIR . $name))) {
                return file_get_contents(self::CACHE_DIR . $name);
            }
        }

        return '';
    }

    /**
     * Создает кэш
     *
     * @param string $name
     * @param string $content
     * @param string $id
     * @param string $prefix - Параметр для отладки и тестов, чтобы отличить контент который берется из кэша
     */
    protected function createCache(string $name, string $content, string $id = '', string $prefix = ''): void
    {
        if ($id) {
            $name .= '_' . $id;
        }

        $file = fopen(self::CACHE_DIR . $name, 'wb');
        fwrite($file, $content . $prefix);
        fclose($file);
    }

    /**
     * Удаляет кэш
     *
     * @param string $name
     * @throws AppException
     */
    protected function deleteCache(string $name): void
    {
        if (!file_exists(self::CACHE_DIR . $name)) {
            throw new AppException(sprintf(self::ERROR_MISS_CACHE, self::CACHE_DIR . $name));
        }

        if ($name) {
            unlink(self::CACHE_DIR . $name);
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
     * TODO Переделать с учетом ухода от методов на __invoke()
     *
     * @param string $controllerAction
     * @param string $id
     * @param int $time
     * @param string $prefix
     * @return string
     */
    protected function cacheWrapper(string $controllerAction, string $id = '', $time = 0, string $prefix = ''): string
    {
        $content = $this->getCache($controllerAction, $time, $id);

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
