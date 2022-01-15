<?php

namespace NW\Route;

use NW\Request\Request;

class Route
{
    // TODO Перевести все свойства в приватные

    /**
     * @var string - Название маршрута
     */
    public $name;

    /**
     * @var string - Путь маршрута
     */
    public $path;

    /**
     * @var string - Контроллер и его метод, которые будут обрабатывать данный запрос
     */
    public $handler;

    /**
     * @var string - Метод запроса - GET | POST | ...
     */
    public $method;

    /**
     * Массив с правилами валидации параметров из URI, например: /blog/{id} где id может быть только числом:
     * $params = ['id' => '\d+']
     *
     * Другой пример: /catalog/{category}/{page} - где категория - только буквы, а page - только число:
     * $params = ['category' => '[a-z]*', 'page' => '\d+']
     *
     * @var array
     */
    public $params = [];

    /**
     * @var - Дополнительный namespace, чтобы можно было группировать контроллеры по дирректориям
     */
    public $namespace;

    /**
     * @var array - Массив посредников, которые будут обрабатываться перед выполнением конечного экшена в контроллере
     */
    public $middleware = [];

    /**
     * Создает новый маршрут
     *
     * @param string $name - Имя маршрута
     * @param string $path - URI маршрута, вида /blog или /blog/{id}
     * @param string $handler - Контроллер и метод, который будет обрабатывать маршрут, в виде Controller@method
     * @param string $method - HTTP-метод маршрута
     * @param array $params - если из URI необходимо получить какой-то параметр - он указывается здесь
     * @param $namespace
     */
    public function __construct(string $name, string $path, string $handler, string $method, array $params = [], $namespace = null)
    {
        $this->name = $name;
        $this->path = $path;
        $this->handler = $namespace ? $namespace . '\\'. $handler : $handler;
        $this->method = $method;
        $this->params = $params;
        $this->namespace = $namespace;
    }

    /**
     * Принимает Request и проверяет, соответствует ли маршрут запросу
     *
     * Проверка происходит в несколько этапов:
     * 1. Проверяем соответствие метода HTTP-запроса
     * 2. Заменяем uri вида '/blog/10' на '/blog/(?P<id>\d+)'
     * 3. Делаем поиск по uri, если ничего не находит - значит uri не соответствует path в роутере
     *
     * Маршрут может содержать любое количество параметров, например: site.ru/{city}/{catalog}/{page}
     *
     * @param Request $request
     * @return null|array
     */
    public function match(Request $request): ?array
    {
        if ($request->getMethod() !== $this->method) {
            return null;
        }

        if (!$this->params && $request->getUri() === $this->path) {
            return [
                'handler' => $this->handler,
                'request' => $request,
            ];
        }

        $replace = [];
        foreach ($this->params as $key => $value) {
            $replace['{' . $key . '}'] = "(?P<$key>$value)";
        }

        if (!preg_match_all('~^' . str_replace(array_keys($replace), $replace, $this->path) . '$~i', $request->getUri(), $matches)) {
            return null;
        }

        foreach ($this->params as $key => $value) {
            $request->withAttribute($key, $value === '\d+' ? (int)$matches[$key][0] : $matches[$key][0]);
        }

        return [
            'handler' => $this->handler,
            'request' => $request,
        ];
    }

    /**
     * Добавляет middleware для данного маршрута
     *
     * TODO Механика подразумевает передачу только имени класса - переделать на полный пуст вида Class::class
     *
     * @param string $middleware
     * @return Route
     */
    public function addMiddleware(string $middleware): Route
    {
        $this->middleware[] = $middleware;

        return $this;
    }

    /**
     * Если маршрут содержит middleware - выполняет их
     *
     * @param Request $request
     */
    public function runMiddleware(Request $request): void
    {
        if (count($this->middleware) > 0) {

            foreach ($this->middleware as $middleware) {
                $middleware = 'Middleware\\' . $middleware;
                $middleware = new $middleware();
                $middleware($request);
            }
        }
    }
}
