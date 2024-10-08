<?php

declare(strict_types=1);

namespace Tests\src;

use Exception;
use WalkWeb\NW\AbstractHandler;
use WalkWeb\NW\AppException;
use WalkWeb\NW\Response;
use Tests\AbstractTest;
use Tests\utils\ExampleHandler;

class HandlerTest extends AbstractTest
{
    /**
     * @throws Exception
     */
    public function testHandlerGetContainer(): void
    {
        $container = $this->getContainer();
        $controller = new ExampleHandler($container);

        self::assertEquals($container, $controller->getContainer());
    }

    /**
     * @throws Exception
     */
    public function testHandlerMissedView(): void
    {
        $controller = new ExampleHandler($this->getContainer());

        // Дальше в ошибке указывается полный путь к view, но он будет разным в зависимости от размещения проекта
        $this->expectException(AppException::class);
        $this->expectExceptionMessage('View missing');
        $controller->render('unknown_view');
    }

    /**
     * @throws Exception
     */
    public function testHandlerMissedLayout(): void
    {
        $controller = new ExampleHandler($this->getContainer());
        $layout = 'unknown_layout';

        $controller->setLayoutUrl($layout);

        // Дальше в ошибке указывается полный путь к layout, но он будет разным в зависимости от размещения проекта
        $this->expectException(AppException::class);
        $this->expectExceptionMessage(sprintf(AbstractHandler::ERROR_MISS_LAYOUT, $layout));
        $controller->render('errors/404');
    }

    /**
     * @dataProvider jsonDataProvider
     * @param array $data
     * @throws Exception
     */
    public function testHandlerJson(array $data): void
    {
        $controller = new ExampleHandler($this->getContainer());

        $response = $controller->json($data);

        self::assertEquals(Response::OK, $response->getStatusCode());
        self::assertEquals('1.1', $response->getProtocolVersion());
        self::assertEquals('OK', $response->getReasonPhase());
        self::assertEquals(json_encode($data, JSON_THROW_ON_ERROR), $response->getBody());
        self::assertEquals(['Content-Type' => 'application/json'], $response->getHeaders());
    }

    /**
     * @throws Exception
     */
    public function testHandlerErrorPage(): void
    {
        $controller = new ExampleHandler($this->getContainer());

        $response = $controller->renderErrorPage();

        self::assertEquals(Response::NOT_FOUND, $response->getStatusCode());
        self::assertEquals('1.1', $response->getProtocolVersion());
        self::assertEquals('Not Found', $response->getReasonPhase());
        self::assertEquals([], $response->getHeaders());

        // Из-за вывода статистики runtime невозможно точно предугадать, какой будет body
        // По этому пока просто проверяем, что получили строку
        self::assertIsString($response->getBody());
    }

    /**
     * @throws AppException
     */
    public function testHandlerGetCache(): void
    {
        $cacheName = 'name';
        $cacheTime = 100;
        $cacheId = 'uuid';
        $cacheContent = 'content';
        $cachePrefix = '_cache';
        $controller = new ExampleHandler($this->getContainer());

        // Если тест запускается не первый раз - то кэш уже будет, соответственно его нужно удалить
        $cacheFile = $this->getContainer()->getCacheDir() . AbstractHandler::CACHE_DIR . $cacheName . '_' . $cacheId;

        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }

        // Вначале кэш отсутствует
        self::assertEquals('', $controller->getCache($cacheName, $cacheTime, $cacheId));

        // Создаем кэш
        $controller->createCache($cacheName, $cacheContent, $cacheId, $cachePrefix);

        self::assertEquals($cacheContent . $cachePrefix, $controller->getCache($cacheName, $cacheTime, $cacheId));
    }

    /**
     * Тест на удаление кэша
     *
     * @throws Exception
     */
    public function testHandlerDeleteCache(): void
    {
        // Вначале нам нужно создать кэш - код повторяет тест выше
        $cacheName = 'name';
        $cacheTime = 100;
        $cacheId = 'uuid';
        $cacheContent = 'content';
        $cachePrefix = '_cache';
        $controller = new ExampleHandler($this->getContainer());

        // Если тест запускается не первый раз - то кэш уже будет, соответственно его нужно удалить
        $cacheFile = $this->getContainer()->getCacheDir() . AbstractHandler::CACHE_DIR . $cacheName . '_' . $cacheId;

        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }

        // Вначале кэш отсутствует
        self::assertEquals('', $controller->getCache($cacheName, $cacheTime, $cacheId));

        // Создаем кэш
        $controller->createCache($cacheName, $cacheContent, $cacheId, $cachePrefix);

        self::assertEquals($cacheContent . $cachePrefix, $controller->getCache($cacheName, $cacheTime, $cacheId));

        // Теперь удаляем кэш
        $controller->deleteCache($cacheName . '_' . $cacheId);

        // Проверяем, что кэш исчез
        self::assertEquals('', $controller->getCache($cacheName, $cacheTime, $cacheId));
    }

    /**
     * Тест на попытку удалить несуществующий кэш
     *
     * @throws Exception
     */
    public function testHandlerDeleteMissedCache(): void
    {
        $controller = new ExampleHandler($this->getContainer());
        $cacheName = 'unknown_cache';

        // Дальше в ошибке указывается полный путь к кэшу, но он будет разным в зависимости от размещения проекта
        $this->expectException(AppException::class);
        $this->expectExceptionMessage('Cache missing');
        $controller->deleteCache($cacheName);
    }

    /**
     * Тест на метод cacheHTML, который автоматически проверяет наличие кэша, если он есть - возвращает, а если его нет
     * - создает
     *
     * @throws Exception
     */
    public function testHandlerCacheHTML(): void
    {
        $controllerAction = 'exampleAction';
        $cacheId = 'uuid2';
        $cacheTime = 100;
        $cachePrefix = '_cache';
        $controller = new ExampleHandler($this->getContainer());
        $content = 'example html content';

        // Если тест запускается не первый раз - то кэш уже будет, соответственно его нужно удалить
        $cacheFile = $this->getContainer()->getCacheDir() . AbstractHandler::CACHE_DIR . $controllerAction . '_' . $cacheId;

        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }

        // Контент создается первый раз, и возвращается он же. Соответственно префикс кэша отсутствует
        self::assertEquals($content, $controller->cacheWrapper($controllerAction, $cacheId, $cacheTime, $cachePrefix));

        // А теперь обращаемся еще раз - и получаем уже кэш (контент с префиксом)
        self::assertEquals($content . $cachePrefix, $controller->cacheWrapper($controllerAction, $cacheId, $cacheTime, $cachePrefix));
    }

    /**
     * @throws Exception
     */
    public function testHandlerRedirect(): void
    {
        $redirectUrl = 'http://example.com';
        $controller = new ExampleHandler($this->getContainer());

        // Редирект с дефолтными параметрами
        $response = $controller->redirect($redirectUrl);

        self::assertEquals(Response::FOUND, $response->getStatusCode());
        self::assertEquals(['Location' => $redirectUrl], $response->getHeaders());
        self::assertEquals('', $response->getBody());

        // Редирект с пользовательским body и кодом ответа (например, нужно вернуть 301, а не 302)
        $body = 'redirect body';
        $responseCode = Response::MOVED_PERMANENTLY;

        $response = $controller->redirect($redirectUrl, $responseCode, $body);

        self::assertEquals($responseCode, $response->getStatusCode());
        self::assertEquals(['Location' => $redirectUrl], $response->getHeaders());
        self::assertEquals($body, $response->getBody());
    }

    /**
     * @throws AppException
     */
    public function testHandlerTranslate(): void
    {
        $controller = new ExampleHandler($this->getContainer());

        self::assertEquals('Главная', $controller->translate('Home'));
    }

    /**
     * @return array
     */
    public function jsonDataProvider(): array
    {
        return [
            [
                [
                    'content' => 'test json content',
                ],
            ],
            [
                [
                    'version' => '1.0',
                    'content' => ['param1', 'param2', 'param3'],
                ],
            ],
        ];
    }
}
