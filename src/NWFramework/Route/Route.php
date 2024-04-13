<?php

namespace NW\Route;

use NW\Request;

class Route
{
    public const DEFAULT_PRIORITY = 50;

    /**
     * @var string - Название маршрута
     */
    private string $name;

    /**
     * @var string - Путь маршрута
     */
    private string $path;

    /**
     * @var string - Контроллер и его метод, которые будут обрабатывать данный запрос
     */
    private string $handler;

    /**
     * @var string - Метод запроса - GET | POST | ...
     */
    private string $method;

    /**
     * Массив с правилами валидации параметров из URI, например: /blog/{id} где id может быть только числом:
     * $params = ['id' => '\d+']
     *
     * Другой пример: /catalog/{category}/{page} - где категория - только буквы, а page - только число:
     * $params = ['category' => '[a-z]*', 'page' => '\d+']
     *
     * @var array
     */
    private array $params;

    /**
     * Дополнительный namespace, чтобы можно было группировать контроллеры по дирректориям
     *
     * @var string
     */
    private string $namespace;

    /**
     * @var array - Массив посредников, которые будут обрабатываться перед выполнением хандлера
     */
    private array $middleware = [];

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
    public function __construct(string $name, string $path, string $handler, string $method, array $params = [], string $namespace = '')
    {
        $this->name = $name;
        $this->path = $path;
        $this->handler = $namespace ? $namespace . '\\' . $handler : $handler;
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
                'handler'    => $this->handler,
                'request'    => $request,
                'middleware' => $this->middleware,
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
            'handler'    => $this->handler,
            'request'    => $request,
            'middleware' => $this->middleware,
        ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getHandler(): string
    {
        return $this->handler;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @return mixed
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Очередность выполнения:
     *
     * $routes->get('test', '/', 'ExampleHandler')
     *     ->addMiddleware('Middleware1')
     *     ->addMiddleware('Middleware2')
     *     ->addMiddleware('Middleware3')
     * ;
     *
     * Будет аналогична:
     *
     * $routes->get('test', '/', 'ExampleHandler')
     *     ->addMiddleware('Middleware1', 100)
     *     ->addMiddleware('Middleware2', 90)
     *     ->addMiddleware('Middleware3', 80)
     * ;
     *
     * Вначале выполняется первый добавленный Middleware, либо с наибольшим приоритетом.
     *
     * @param string $middleware
     * @param int $priority
     * @return $this
     */
    public function addMiddleware(string $middleware, int $priority = self::DEFAULT_PRIORITY): self
    {
        $this->middleware[$this->currentPriority($priority)] = $middleware;

        return $this;
    }

    /**
     * @return array
     */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    /**
     * Корректирует приоритет middleware - если уже существует middleware с таким приоритетом, то уменьшает указанный
     * приоритет на 1 и пробует добавить middleware еще раз.
     *
     * @param int $priority
     * @return int
     */
    private function currentPriority(int $priority): int
    {
        if (!array_key_exists($priority, $this->middleware)) {
            return $priority;
        }

        $priority--;
        return $this->currentPriority($priority);
    }
}
