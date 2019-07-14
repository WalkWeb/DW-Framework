<?php

namespace NW\Route;

use NW\Request\Request;

class Route
{
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
     * @var array - Необходимый параметр, значение которого нужно получить из URI. Например id статьи
     */
    public $param;

    /**
     * @var - Регулярка для проверка корректности нужного параметра из URI
     */
    public $rules;

    /**
     * @var bool - Указывает, является ли нужный параметр в URI числом
     */
    public $paramNumber = false;

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
     * @param array $param - если из URI необходимо получить какой-то параметр - он указывается здесь
     * @param $namespace
     */
    public function __construct(string $name, string $path, string $handler, string $method, array $param = [], $namespace = null)
    {
        $this->name = $name;
        $this->path = $path;
        $this->handler = $namespace ? $namespace . '\\'. $handler : $handler;
        $this->method = $method;
        $this->setParam($param);
        $this->namespace = $namespace;
    }

    /**
     * Принимает Request и проверяет, есть ли маршрут соответствующий этому запросу
     *
     * Проверка происходит в несколько этапов:
     * 1. Проверяем соответствие метода HTTP-запроса
     * 2. Заменяем uri вида '/blog/10' на '/blog/(?P<id>\d+)'
     * 3. Делаем поиск по uri, если ничего не находит - значит uri не соответствует path в роутере
     *
     * @param Request $request
     * @return null|array
     */
    public function match(Request $request): ?array
    {
        if ($request->getMethod() !== $this->method) {
            return null;
        }

        if (!$this->param && $request->getUri() === $this->path) {
            return [
                'handler' => $this->handler,
                'request' => $request,
            ];
        }

        if (!preg_match('~^' . preg_replace('~\{([^\}]+)\}~', '(?P<' . $this->param . '>' . $this->rules . ')', $this->path) . '$~i', $request->getUri(), $matches)) {
            return null;
        }

        $request->withAttribute($this->param, $this->paramNumber ? (int)$matches[$this->param] : $matches[$this->param]);

        return [
            'handler' => $this->handler,
            'request' => $request,
        ];
    }

    /**
     * Добавляет middleware для данного маршрута
     *
     * @param string $middleware
     * @return Route
     */
    public function middleware(string $middleware): Route
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
                $middleware = new $middleware($request);
                $middleware();
            }

        }
    }

    /**
     * Если нужный параметр в URI число - задаем это в свойствах, чтобы в match() сделать по нему проверку и по
     * необходимости привести к int
     *
     * @param array $param
     * @return array
     */
    private function setParam(array $param): array
    {
        foreach ($param as $key => $value) {
            $this->param = $key;
            $this->rules = $value;
            if ($value === '\d+') {
                $this->paramNumber = true;
            }
        }

        return $param;
    }
}
