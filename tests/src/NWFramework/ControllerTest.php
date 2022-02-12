<?php

declare(strict_types=1);

namespace Tests\src\NWFramework;

use Exception;
use NW\Utils\HttpCode;
use Tests\AbstractTestCase;
use Tests\utils\ExampleController;

class ControllerTest extends AbstractTestCase
{
    /**
     * @throws Exception
     */
    public function testControllerMissedView(): void
    {
        $controller = new ExampleController();

        $this->expectException(\NW\Exception::class);
        $this->expectExceptionMessage(
            'View не найден: /var/www/dw-framework.loc/src/NWFramework/../../views/default/unknown_view.php'
        );
        $controller->render('unknown_view');
    }

    /**
     * @throws Exception
     */
    public function testControllerMissedLayout(): void
    {
        $controller = new ExampleController();

        $controller->setLayoutUrl('unknown_layout');

        $this->expectException(\NW\Exception::class);
        $this->expectExceptionMessage(
            'Layout не найден: /var/www/dw-framework.loc/src/NWFramework/../../views/default/errors/404.php'
        );
        $controller->render('errors/404');
    }

    /**
     * @dataProvider jsonDataProvider
     * @param array $data
     * @throws Exception
     */
    public function testControllerJson(array $data): void
    {
        $controller = new ExampleController();

        $response = $controller->json($data);

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('1.1', $response->getProtocolVersion());
        self::assertEquals('OK', $response->getReasonPhase());
        self::assertEquals(json_encode($data, JSON_THROW_ON_ERROR), $response->getBody());
        self::assertEquals(['Content-Type' => 'application/json'], $response->getHeaders());
    }

    /**
     * @throws Exception
     */
    public function testControllerErrorPage(): void
    {
        $controller = new ExampleController();

        $response = $controller->renderErrorPage();

        self::assertEquals(404, $response->getStatusCode());
        self::assertEquals('1.1', $response->getProtocolVersion());
        self::assertEquals('Not Found', $response->getReasonPhase());
        self::assertEquals([], $response->getHeaders());

        // Из-за вывода статистики runtime невозможно точно предугадать, какой будет body
        // По этому пока просто проверяем, что получили строку
        self::assertIsString($response->getBody());
    }

    public function testControllerCheckCache(): void
    {
        $cacheName = 'name';
        $cacheTime = 100;
        $cacheId = 'uuid';
        $cacheContent = 'content';
        $cachePrefix = '_cache';
        $controller = new ExampleController();

        // Если тест запускается не первый раз - то кэш уже будет, соответственно его нужно удалить
        $cacheFile = __DIR__ . '/../../../cache/html/' . $cacheName . '_' . $cacheId;

        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }

        // Вначале кэш отсутствует
        self::assertFalse($controller->checkCache($cacheName, $cacheTime, $cacheId));

        // Создаем кэш
        $controller->createCache($cacheName, $cacheContent, $cacheId, $cachePrefix);

        self::assertEquals($cacheContent . $cachePrefix, $controller->checkCache($cacheName, $cacheTime, $cacheId));
    }

    /**
     * Тест на удаление кэша
     *
     * @throws Exception
     */
    public function testControllerDeleteCache(): void
    {
        // Вначале нам нужно создать кэш - код повторяет тест выше
        $cacheName = 'name';
        $cacheTime = 100;
        $cacheId = 'uuid';
        $cacheContent = 'content';
        $cachePrefix = '_cache';
        $controller = new ExampleController();

        // Если тест запускается не первый раз - то кэш уже будет, соответственно его нужно удалить
        $cacheFile = __DIR__ . '/../../../cache/html/' . $cacheName . '_' . $cacheId;

        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }

        // Вначале кэш отсутствует
        self::assertFalse($controller->checkCache($cacheName, $cacheTime, $cacheId));

        // Создаем кэш
        $controller->createCache($cacheName, $cacheContent, $cacheId, $cachePrefix);

        self::assertEquals($cacheContent . $cachePrefix, $controller->checkCache($cacheName, $cacheTime, $cacheId));

        // Теперь удаляем кэш
        $controller->deleteCache($cacheName . '_' . $cacheId);

        // Проверяем, что кэш исчез
        self::assertFalse($controller->checkCache($cacheName, $cacheTime, $cacheId));
    }

    /**
     * Тест на попытку удалить несуществующий кэш
     *
     * @throws Exception
     */
    public function testControllerDeleteMissedCache(): void
    {
        $controller = new ExampleController();
        $cacheName = 'unknown_cache';

        $this->expectException(\NW\Exception::class);
        $this->expectExceptionMessage(
            'Указанного кэша не существует: /var/www/dw-framework.loc/src/NWFramework/../../cache/html/' .
            $cacheName
        );
        $controller->deleteCache($cacheName);
    }

    /**
     * Тест на метод cacheHTML, который автоматически проверяет наличие кэша, если он есть - возвращает, а если его нет
     * - создает
     *
     * @throws Exception
     */
    public function testControllerCacheHTML(): void
    {
        $controllerAction = 'exampleAction';
        $cacheId = 'uuid2';
        $cacheTime = 100;
        $cachePrefix = '_cache';
        $controller = new ExampleController();
        $content = 'example html content';

        // Если тест запускается не первый раз - то кэш уже будет, соответственно его нужно удалить
        $cacheFile = __DIR__ . '/../../../cache/html/' . $controllerAction . '_' . $cacheId;

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
    public function testControllerRedirect(): void
    {
        $redirectUrl = 'http://example.com';

        $controller = new ExampleController();

        // Редирект с дефолтными параметрами
        $response = $controller->redirect($redirectUrl);

        self::assertEquals(HttpCode::FOUND, $response->getStatusCode());
        self::assertEquals(['Location' => $redirectUrl], $response->getHeaders());
        self::assertEquals('', $response->getBody());

        // Редирект с пользовательским body и кодом ответа (например, нужно вернуть 301, а не 302)
        $body = 'redirect body';
        $responseCode = HttpCode::MOVED_PERMANENTLY;

        $response = $controller->redirect($redirectUrl, $body, $responseCode);

        self::assertEquals($responseCode, $response->getStatusCode());
        self::assertEquals(['Location' => $redirectUrl], $response->getHeaders());
        self::assertEquals($body, $response->getBody());
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
